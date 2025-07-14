<?php

namespace App\Http\Controllers;

use App\Domains\System\Repositories\FileAttachmentRepository;
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
        // 첫 화면은 비어 있는 대시보드 HTML만 렌더
        return $this->render('admin.pages.dashboard');
    }

    public function dashboardData()
    {
        if (user()->isDealer()) {
            return $this->dealerDashboardApi();
        }

        // 관리자용 데이터
        $recentOrders = \App\Domains\Order\Repositories\OrderRepository::make()
            ->query()
            ->with(['user.dealer'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $notices = \App\Domains\Communication\Repositories\NoticeRepository::make()
            ->query()
            ->where('status', \App\Domains\Communication\Enums\NoticeStatus::PUBLISHED)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $dealerSales = \App\Domains\Order\Repositories\OrderRepository::make()
            ->query()
            ->selectRaw('dealer_id, SUM(total_amount) as total_sales, MAX(created_at) as last_order_date')
            ->groupBy('dealer_id')
            ->orderByDesc('last_order_date')
            ->get();

        $monthlySales = \App\Domains\Order\Repositories\OrderRepository::make()
            ->query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as total_sales")
            ->groupBy("month")
            ->orderBy("month")
            ->get();

        return $this->render(null, [
            'recentOrders' => $recentOrders,
            'notices' => $notices,
            'dealerSales' => $dealerSales,
            'monthlySales' => $monthlySales,
        ], '데이터 로드 완료');
    }

    protected function dealerDashboardApi()
    {
        $dealerId = user()->dealer->id;

        $totalSales = \App\Domains\Order\Repositories\OrderRepository::make()
            ->query()
            ->where('dealer_id', $dealerId)
            ->sum('total_amount');

        $recentOrders = \App\Domains\Order\Repositories\OrderRepository::make()
            ->query()
            ->where('dealer_id', $dealerId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $notices = \App\Domains\Communication\Repositories\NoticeRepository::make()
            ->query()
            ->where('status', \App\Domains\Communication\Enums\NoticeStatus::PUBLISHED)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return $this->render(null, [
            'totalSales' => $totalSales,
            'recentOrders' => $recentOrders,
            'notices' => $notices,
        ], '데이터 로드 완료');
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
