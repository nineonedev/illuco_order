<?php

namespace App\Http\Controllers;

use App\Domains\Communication\Repositories\NoticeRepository;
use App\Domains\Order\Repositories\OrderRepository;
use App\Domains\System\Repositories\FileAttachmentRepository;
use App\Domains\User\Repositories\UserRepository;
use Framework\Routing\Controller;

class AdminController extends Controller
{
    public function home()
    {
        return $this->redirectRoute('auth.signin');
    }

    public function guide()
    {
        return $this->render('admin.pages.guide');
    }

    public function dashboard()
    {
        $notices = NoticeRepository::make()
            ->with(['user'])
            ->query()
            ->limit(10)
            ->get();

        $orderQuery = OrderRepository::make()
            ->with([
                'customer',
                'user',
                'items.product' => [
                    'template' => [
                        'fileattachment',
                        'category'
                    ],
                    'loupe',
                    'headlight',
                ]
            ])
            ->query();

        if (user()->isDealer()) {
            $orderQuery->where('dealer_id', user()->dealer->id);
        }
        
        $orders = $orderQuery->limit(10)->get();

        $yearsRow = db('orders')
            ->selectRaw('MIN(YEAR(created_at)) as min_year, MAX(YEAR(created_at)) as max_year')
            ->first();

        $years = [];

        if ($yearsRow && $yearsRow->min_year && $yearsRow->max_year) {
            $years = range((int)$yearsRow->min_year, (int)$yearsRow->max_year);
        }

        $selectedYear = null;

        if ($yearsRow && $yearsRow->min_year && $yearsRow->max_year) {
            $minYear = (int)$yearsRow->min_year;
            $maxYear = (int)$yearsRow->max_year;

            $years = range($minYear, $maxYear);

            // 쿼리스트링에서 넘어온 연도
            $queryYear = request()->query('year');

            if ($queryYear !== null && $queryYear !== '') {
                $queryYear = (int)$queryYear;

                if ($queryYear >= $minYear && $queryYear <= $maxYear) {
                    $selectedYear = $queryYear;
                } else {
                    // 범위를 벗어나면 maxYear 우선 사용
                    $selectedYear = $maxYear ?? $minYear;
                }
            } else {
                // 쿼리 없으면 maxYear 우선 사용
                $selectedYear = $maxYear;
            }
        }

        return $this->render('admin.pages.dashboard', [
            'orders' => $orders,
            'notices' => $notices,
            'years' => $years,
            'year' => $selectedYear,
        ]);
    }

    public function aggregateApi()
    {
        $buyerType = request()->query('buyerType');
        $buyerId = request()->query('buyerId');
        $year = request()->query('year');
        $month = request()->query('month');

        $aggregation = $this->runAggregate(
            $buyerType,
            $buyerId ? (int)$buyerId : null,
            $year ? (int)$year : null,
            $month ? (int)$month : null
        );

        return $this->render(null, [
            'aggregation' => $aggregation
        ]);
    }

    public function aggregate(
        ?string $buyerType = null,
        ?int $buyerId = null,
        ?int $year = null,
        ?int $month = null
    ) {
        $aggregation = $this->runAggregate(
            $buyerType,
            $buyerId,
            $year,
            $month
        );

        return $this->render(null, [
            'aggregation' => $aggregation,
        ], '집계 완료');
    }


    protected function runAggregate(
        ?string $buyerType = null,
        ?int $buyerId = null,
        ?int $year = null,
        ?int $month = null
    ) {
        $totalSalesData = $this->aggregateTotalSales($year, $month);
        
        $dealerId = user()->isDealer() ? user()->dealer->id : null;

        $dealerSales = $this->aggregateDealerSales(
            $year,
            $month,
            $dealerId
        );
        
        $illucoSalesData = $this->aggregateIllucoSales($year, $month);

        $currentYear = date('Y');
        $yearsList = [];
        for ($y = $currentYear - 5; $y <= $currentYear; $y++) {
            $yearsList[] = (int)$y;
        }

        $queryInfo = [
            'buyerType' => $buyerType,
            'buyerId' => $buyerId,
            'year' => $year,
            'month' => $month,
        ];

        return (object)[
            'query' => $queryInfo,
            'years' => $yearsList,
            'total_sales' => $totalSalesData,
            'dealer_sales' => $dealerSales,
            'illuco_sales' => $illucoSalesData,
        ];
    }

    protected function aggregateTotalSales(?int $year = null, ?int $month = null): array
    {
        $query = db('orders');

        if ($year) {
            $query->whereRaw("YEAR(created_at) = ?", [$year]);
        }
        if ($month) {
            $query->whereRaw("MONTH(created_at) = ?", [$month]);
        }

        $totalAmount = $query->clone()->sum('total_amount');

        $years = [];
        $rows = db('orders')
            ->selectRaw("YEAR(created_at) as year, SUM(total_amount) as total_sales")
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        foreach ($rows as $row) {
            $years[] = [
                'year' => (int)$row->year,
                'total_sales' => (float)$row->total_sales,
            ];
        }

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = [
                'month' => $m,
                'total_sales' => 0.0,
            ];
        }

        $rows = db('orders')
            ->when($year, fn($q) => $q->whereRaw("YEAR(created_at) = ?", [$year]))
            ->selectRaw("YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as total_sales")
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        foreach ($rows as $row) {
            $months[(int)$row->month]['total_sales'] = (float)$row->total_sales;
        }

        return [
            'total_sales' => (float)$totalAmount,
            'years' => $years,
            'months' => array_values($months),
        ];
    }

    protected function aggregateIllucoSales(?int $year = null, ?int $month = null): array
    {
        $query = db('orders')->whereNull('dealer_id');

        if ($year) {
            $query->whereRaw("YEAR(created_at) = ?", [$year]);
        }
        if ($month) {
            $query->whereRaw("MONTH(created_at) = ?", [$month]);
        }

        $totalAmount = $query->sum('total_amount');

        // years
        $years = db('orders')
            ->whereNull('dealer_id')
            ->selectRaw("YEAR(created_at) as year, SUM(total_amount) as total_sales")
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $yearsResult = [];
        foreach ($years as $row) {
            $yearsResult[] = [
                'year' => (int)$row->year,
                'total_sales' => (float)$row->total_sales,
            ];
        }

        // months
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = [
                'month' => $m,
                'total_sales' => 0.0,
            ];
        }

        $rows = db('orders')
            ->whereNull('dealer_id')
            ->when($year, fn($q) => $q->whereRaw("YEAR(created_at) = ?", [$year]))
            ->selectRaw("YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as total_sales")
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        foreach ($rows as $row) {
            $months[(int)$row->month]['total_sales'] = (float)$row->total_sales;
        }

        return [
            'total_sales' => (float)$totalAmount,
            'years' => $yearsResult,
            'months' => array_values($months),
        ];
    }

    protected function aggregateDealerSales(
        ?int $year = null,
        ?int $month = null,
        ?int $specificDealerId = null
    ): array {
        $query = db('orders')->whereNotNull('dealer_id');

        if ($specificDealerId) {
            $query->where('dealer_id', $specificDealerId);
        }

        if ($year) {
            $query->whereRaw("YEAR(created_at) = ?", [$year]);
        }
        if ($month) {
            $query->whereRaw("MONTH(created_at) = ?", [$month]);
        }

        $rows = $query
            ->selectRaw("
                dealer_id,
                SUM(total_amount) as total_sales,
                MAX(created_at) as last_order_at
            ")
            ->groupBy('dealer_id')
            ->orderByDesc('last_order_at')
            ->get();

        $dealers = UserRepository::make()->query()
            ->with(['dealer'])
            ->where('type', 'dealer')
            ->get();

        $dealerList = [];
        foreach ($dealers as $user) {
            if ($user->dealer) {
                $dealerList[$user->dealer->id] = $user->name;
            }
        }

        $result = [];

        foreach ($rows as $row) {
            $dealerId = $row->dealer_id;

            // per dealer - years
            $yearsRows = db('orders')
                ->where('dealer_id', $dealerId)
                ->selectRaw("YEAR(created_at) as year, SUM(total_amount) as total_sales")
                ->groupBy('year')
                ->orderBy('year')
                ->get();

            $years = [];
            foreach ($yearsRows as $yr) {
                $years[] = [
                    'year' => (int)$yr->year,
                    'total_sales' => (float)$yr->total_sales,
                ];
            }

            // per dealer - months
            $months = [];
            for ($m = 1; $m <= 12; $m++) {
                $months[$m] = [
                    'month' => $m,
                    'total_sales' => 0.0,
                ];
            }

            $monthsRows = db('orders')
                ->where('dealer_id', $dealerId)
                ->when($year, fn($q) => $q->whereRaw("YEAR(created_at) = ?", [$year]))
                ->selectRaw("YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as total_sales")
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            foreach ($monthsRows as $mo) {
                $months[(int)$mo->month]['total_sales'] = (float)$mo->total_sales;
            }

            $result[] = [
                'dealer_id' => $dealerId,
                'dealer_name' => $dealerList[$dealerId] ?? '-',
                'total_sales' => (float)$row->total_sales,
                'years' => $years,
                'months' => array_values($months),
            ];
        }

        return $result;
    }


    public function test()
    {
        return $this->render('admin.pages.test');
    }

    public function setting()
    {
        return $this->render('admin.pages.setting');
    }

    public function upload()
    {
        $file = FileAttachmentRepository::make()->uploadWithoutEntity('file');

        if (!$file) {
            return $this->renderError(null, '파일 업로드에 실패했습니다.');
        }

        return $this->render(null, ['file' => $file], '정상적으로 업로드되었습니다.');
    }
}
