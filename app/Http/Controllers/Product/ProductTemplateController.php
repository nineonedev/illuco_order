<?php

namespace App\Http\Controllers\Product;

use App\Domains\Product\Entities\Headlight;
use App\Domains\Product\Entities\Loupe;
use App\Domains\Product\Entities\ProductTemplate;
use App\Domains\Product\Repositories\CategoryRepository;
use App\Domains\Product\Repositories\LoupeSettingRepository;
use App\Domains\Product\Repositories\ProductTemplateRepository;
use App\Domains\System\Entities\FileAttachment;
use App\Domains\System\Repositories\FileAttachmentRepository;
use App\Domains\System\Supports\FileAttachmentList;
use App\Domains\User\Repositories\DealerPriceRepository;
use App\Http\Requests\Product\SaveProductTemplateRequest;
use Framework\Database\ORM\Traits\SoftDeletes;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;


class ProductTemplateController extends Controller
{
    use SoftDeletes; 

    public function repo(): ProductTemplateRepository
    {
        return ProductTemplateRepository::make();
    }
    public function fileRepo(): FileAttachmentRepository
    {
        return FileAttachmentRepository::make();
    }

    public function attributes(Request $request)
    {
        // 1) loupe: 루페 세팅 + 프레임 컬러 기반으로 구성
        $settings = LoupeSettingRepository::make()
            ->with(['template', 'frameColors'])
            ->query()
            ->get();

        $loupe = [];
        foreach ($settings as $setting) {
            $template = $setting->template ?? null;

            // 키는 예시처럼 모델코드(예: ITL-1025G)로 사용: template->code 우선
            $key = $template->model ?? $template->code ?? null;
            if (!$key) {
                continue;
            }

            // working_distance: WD(cm) 범위
            $wdMin = is_numeric($setting->wd_min) ? (float)$setting->wd_min : null;
            $wdMax = is_numeric($setting->wd_max) ? (float)$setting->wd_max : null;
            $workingDistance = null;
            if ($wdMin !== null && $wdMax !== null) {
                $workingDistance = [
                    'label' => sprintf('%s~%scm', rtrim(rtrim(number_format($wdMin, 1), '0'), '.'), rtrim(rtrim(number_format($wdMax, 1), '0'), '.')),
                    'min'   => (float)$wdMin,
                    'max'   => (float)$wdMax,
                ];
            }

            // [ADD] PD/VD 스펙 구성 (mm 단위 라벨)
            $pdRight = null;
            if (is_numeric($setting->pd_right_min) && is_numeric($setting->pd_right_max)) {
                $rMin = (float)$setting->pd_right_min;
                $rMax = (float)$setting->pd_right_max;
                $pdRight = [
                    'label' => sprintf('%s~%smm',
                        rtrim(rtrim(number_format($rMin, 1), '0'), '.'),
                        rtrim(rtrim(number_format($rMax, 1), '0'), '.')
                    ),
                    'min' => $rMin,
                    'max' => $rMax,
                ];
            }


            $pdLeft = null;
            if (is_numeric($setting->pd_left_min) && is_numeric($setting->pd_left_max)) {
                $lMin = (float)$setting->pd_left_min;
                $lMax = (float)$setting->pd_left_max;
                $pdLeft = [
                    'label' => sprintf('%s~%smm',
                        rtrim(rtrim(number_format($lMin, 1), '0'), '.'),
                        rtrim(rtrim(number_format($lMax, 1), '0'), '.')
                    ),
                    'min' => $lMin,
                    'max' => $lMax,
                ];
            }

            $pdTotal = null;
            if ($pdRight && $pdLeft) {
                $tMin = (float)$pdRight['min'] + (float)$pdLeft['min'];
                $tMax = (float)$pdRight['max'] + (float)$pdLeft['max'];
                $fmt = function ($n) { return rtrim(rtrim(number_format($n, 1), '0'), '.'); };
                $pdTotal = [
                    'label' => sprintf('%s~%smm', $fmt($tMin), $fmt($tMax)),
                    'min'   => $tMin,
                    'max'   => $tMax,
                ];
            } elseif (is_numeric($setting->pd_total_distance)) {
                // 우/좌 범위가 없고 단일 권장값만 있으면 추천값으로 내려줌
                $v   = (float)$setting->pd_total_distance;
                $fmt = rtrim(rtrim(number_format($v, 1), '0'), '.');
                $pdTotal = [
                    'label'       => sprintf('%smm', $fmt),
                    'recommended' => $v,
                ];
            }

            $vertexDistance = null;
            if (is_numeric($setting->vd_min) && is_numeric($setting->vd_max)) {
                $vdMin = (float)$setting->vd_min;
                $vdMax = (float)$setting->vd_max;
                $vertexDistance = [
                    'label'     => sprintf('%s~%smm',
                        rtrim(rtrim(number_format($vdMin, 1), '0'), '.'),
                        rtrim(rtrim(number_format($vdMax, 1), '0'), '.')
                    ),
                    'min'       => $vdMin,
                    'max'       => $vdMax,
                    'exclusive' => true, // VD는 (min,max) 배타 범위로 사용
                ];
            }

            // frame_type: 세팅에 연결된 활성 컬러를 [label, value] 로 변환
            // value는 color.code (없으면 id 문자열로 대체)
            $frameTypes = [];
            $colors = $setting->frameColors ?? [];
            foreach ($colors as $c) {
                if (isset($c->is_active) && (int)$c->is_active !== 1) {
                    continue; // 비활성 색상은 제외
                }
                $label = $c->name ?? '';
                $value = $c->code ?? (string)($c->id ?? '');
                
                if ($label && $value) {
                    $frameTypes[] = ['label' => $label, 'value' => $value, 'hex' => $c->hex ?? ''];
                }
            }
            if (empty($frameTypes)) {
                $frameTypes = null; // 예시 포맷에 맞춰 컬러 없으면 null 허용
            }

            $loupe[$key] = [
                'frame_type'       => $frameTypes,
                'working_distance' => $workingDistance,
                'vertex_distance'  => $vertexDistance,
                'pd_left'          => $pdLeft,
                'pd_right'         => $pdRight,
                'pd_total'         => $pdTotal, 
            ];
        }

        // 2) headlight: 기존 상수에서 예시 포맷으로 가볍게 맞춤 (없으면 null)
        $headlight = [];
        if (is_array(Headlight::MODEL_SPECS ?? null)) {
            foreach (Headlight::MODEL_SPECS as $modelCode => $spec) {
                $colors = $spec['wireless_colors'] ?? $spec['WIRELESS_COLORS'] ?? null;
                if (is_array($colors)) {
                    // 원소가 string 혹은 ['value','label'] 혼재 가능성 고려
                    $colors = array_map(function ($item) {
                        if (is_array($item)) {
                            return [
                                'value' => $item['value'] ?? ($item['code'] ?? ''),
                                'label' => $item['label'] ?? ($item['name'] ?? ($item['value'] ?? '')),
                            ];
                        }
                        return ['value' => (string)$item, 'label' => (string)$item];
                    }, $colors);
                } else {
                    $colors = null;
                }
                $headlight[$modelCode] = ['wireless_colors' => $colors];
            }
        } else {
            $headlight = null;
        }

        // 3) countries 그대로
        $countries = __('system.countries');

        $data = [
            'loupe'     => $loupe,
            'headlight' => $headlight,
            'countries' => $countries,
        ];

        return $this->render(null, $data, '성공적으로 로드되었습니다.');
    }


    public function labels()
    {
        $data = [
            'loupe' => Loupe::LABELS,
            'headlight' => Headlight::LABELS
        ];

        return $this->render(null, $data, '성공적으로 로드되었습니다.');
    }

    public function setGroupItems()
    {
        $data = [];

        $precisonLens = ProductTemplateRepository::make()
            ->query()
            ->with(['fileattachment'])
            ->where('model', Loupe::PRECISON_LENS)
            ->first();

        if ($precisonLens) {
            $data[$precisonLens->model] = $precisonLens->toArray();
        }

        return $this->render(null, $data, '성공적으로 로드되었습니다.');
    }

    public function loupes(Request $request)
    {
        // 1) loupe 카테고리 조회
        $loupeCategory = CategoryRepository::make()
            ->query()
            ->where('slug', 'loupe')
            ->first();

        // 2) 기본 쿼리 (항상 파일첨부/카테고리 eager load)
        $query = $this->repo()
            ->with([FileAttachment::class, 'category'])
            ->query();

        // loupe 카테고리가 존재하면 해당 id로 강제 필터링,
        // 없으면 결과가 비도록 존재하지 않을 값(-1)로 필터링
        if ($loupeCategory) {
            $query->where('category_id', $loupeCategory->id);
        } else {
            $query->where('category_id', -1);
        }

        // 3) 검색 필터 (index와 동일)
        $query->when(
            $name = $request->query('name'),
            fn($q) => $q->where('name', 'like', "%{$name}%")
        );

        $query->when(
            $code = $request->query('code'),
            fn($q) => $q->where('code', 'like', "%{$code}%")
        );

        $query->when(
            $model = $request->query('model'),
            fn($q) => $q->where('model', 'like', "%{$model}%")
        );

        $query->when(
            $search = $request->query('search'),
            fn($q) => $q->where(function ($qq) use ($search) {
                $qq->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('model', 'like', "%{$search}%");
            })
        );

        // 4) excepts(제외 id들) 지원 (index와 동일)
        if ($excepts = $request->query('excepts')) {
            $exceptIds = array_filter(array_map('intval', explode(',', $excepts)));
            if (!empty($exceptIds)) {
                $query->whereNotIn('id', $exceptIds);
            }
        }

        // 5) 정렬 (index와 동일)
        $sort = $request->query('sort');
        if ($sort) {
            switch ($sort) {
                case 'latest':
                    $query->orderByDesc('created_at');
                    break;
                case 'oldest':
                    $query->orderByAsc('created_at');
                    break;
                case 'name_asc':
                    $query->orderBy('name');
                    break;
                case 'name_desc':
                    $query->orderByDesc('name');
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at');
                    break;
                case 'created_at_desc':
                    $query->orderByDesc('created_at');
                    break;
                case 'sort_order_asc':
                    $query->orderBy('sort_order');
                    break;
                case 'sort_order_desc':
                    $query->orderByDesc('sort_order');
                    break;
                default:
                    $query->orderByDesc('created_at');
                    break;
            }
        } else {
            $query->orderByDesc('sort_order')->orderByDesc('created_at');
        }

        // 6) 페이지네이션
        $perPage   = $request->query('perpage', 15);
        $page      = $request->query('page', 1);
        $paginator = $query->paginate($perPage, $page);
        $templates = $request->expectsJson() ? $paginator->toArray() : $paginator;

        // 7) 뷰 렌더 (isDealer, prices, category_ids 로직 제거)
        //    categories는 loupe만 단일로 내려줌(필요 시 셀렉트에 쓰일 수 있음)
        $categories = $loupeCategory ? [$loupeCategory] : [];

        return $this->render('admin.pages.products.templates.index', [
            'templates'  => $templates,
            'query'      => $request->query(),
            'categories' => $request->expectsJson()
                ? array_map(fn($c) => $c->toArray(), $categories)
                : $categories,
            'category_id' => $loupeCategory ? $loupeCategory->id : null,
        ]);
    }


    public function index(Request $request)
    {
        $query = $this->repo()
            ->with([FileAttachment::class, 'category'])
            ->query();
        
        $hasCategory = user()->isDealer() && user()->dealer->category_ids;

        $categoryIds = $request->query('category_ids') ?? null;
        $categoryIds = $categoryIds ? explode(',', $categoryIds) : [];
            
        $categoryIdsResult = []; 

        if ($hasCategory) {
            $categoryIdsResult = explode(',', user()->dealer->category_ids); 
        } else {
            $categoryIdsResult = $categoryIds; 
        }

        $categoryId = $request->query('category_id');

        // 제품명 검색
        $query->when(
            $name = $request->query('name'),
            fn($q) => $q->where('name', 'like', "%{$name}%")
        );

        // 코드 검색
        $query->when(
            $code = $request->query('code'),
            fn($q) => $q->where('code', 'like', "%{$code}%")
        );

        // 모델 검색
        $query->when(
            $model = $request->query('model'),
            fn($q) => $q->where('model', 'like', "%{$model}%")
        );

        $query->when(
            $search = $request->query('search'),
            fn($q) => $q->where(function ($qq) use ($search) {
                $qq->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('model', 'like', "%{$search}%");
            })
        );

        if ($hasCategory) {
            $query->whereIn('category_id', $categoryIdsResult);
        }
        
        $query->when(
            $categoryId,
            fn($q) =>  $q->where('category_id', $categoryId)
        );

        $excepts = $request->query('excepts');
        if ($excepts) {
            // "1,2,3" -> [1,2,3]
            $exceptIds = array_filter(array_map('intval', explode(',', $excepts)));
            if (!empty($exceptIds)) {
                $query->whereNotIn('id', $exceptIds);
            }
        }
        // $query->when(
        //     $categoryIdsResult,
        //     fn($q) =>  $q->whereIn('category_id', $categoryIdsResult)
        // );

        // 정렬
        $sort = $request->query('sort');
        if ($sort) {
            switch ($sort) {
                case 'latest':
                    $query->orderByDesc('created_at');
                    break;
                case 'oldest':
                    $query->orderByAsc('created_at');
                    break;
                case 'name_asc':
                    $query->orderBy('name');
                    break;
                case 'name_desc':
                    $query->orderByDesc('name');
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at');
                    break;
                case 'created_at_desc':
                    $query->orderByDesc('created_at');
                    break;
                case 'sort_order_asc':
                    $query->orderBy('sort_order');
                    break;
                case 'sort_order_desc':
                    $query->orderByDesc('sort_order');
                    break;
                default:
                    $query->orderByDesc('created_at');
                    break;
            }
        } else {
            $query->orderByDesc('created_at');
        }

        $perPage = $request->query('perpage', 15);
        $page = $request->query('page', 1);

        $paginator = $query->paginate($perPage, $page);

                // ✅ [대리점 전용] 목록 가격에 대리점 단가 반영
        if (user()->isDealer()) {
            $dealerId = (int) user()->dealer->id;

            // [product_template_id => price] 맵 구성
            $dealerPrices = DealerPriceRepository::make()
                ->query()
                ->where('dealer_id', $dealerId)
                ->where('is_active', 1)
                ->get();

            $priceMap = [];
            foreach ($dealerPrices as $dp) {
                $tplId = (int) ($dp->product_template_id ?? 0);
                if ($tplId > 0) {
                    $priceMap[$tplId] = (float) $dp->price;
                }
            }

            $paginator->mutateItems(function ($item) use ($priceMap) {
                $id = (int) ($item->id ?? 0);
                if ($id && isset($priceMap[$id])) {
                    $item->original_price = $item->original_price ?? ($item->price ?? null);
                    $item->dealer_price   = $priceMap[$id];
                    $item->price          = $priceMap[$id];
                }
            });
        }

        // 이후에 JSON 변환
        $templates = $request->expectsJson() ? $paginator->toArray() : $paginator;


        // 카테고리 목록도 같이 내려줌
        $categoryQuery = CategoryRepository::make()
            ->query()
            ->orderByAsc('sort_order');

        if ($hasCategory) {
            $categoryQuery->whereIn('id', $categoryIdsResult);
        }

        $categories = $categoryQuery->get();

        $prices = []; 
        if (user()->isDealer()) {
            $prices = DealerPriceRepository::make()
                ->with(['template'])
                ->query()
                ->where('dealer_id', user()->dealer->id)
                ->get();
            
            foreach ($prices as &$price) {
                $price = $price->toArray();
            }
        }

        return $this->render('admin.pages.products.templates.index', [
            'templates'   => $templates,
            'query'       => $request->query(),
            'categories'  => request()->expectsJson() ? array_map(fn($c) => $c->toArray(), $categories) : $categories,
            'isDealer' => user()->isDealer(),
            'category_id' => $categoryId,
            'prices' => $prices,
        ]);
    }


    public function show(string $id, Request $request) 
    {
        $template = $this->repo()->with([FileAttachment::class, 'category'])->findOrFail($id);

        $template = $request->expectsJson() ? $template->toArray() : $template;
        
        return $this->render('admin.pages.products.templates.show', ['template' => $template]);
    }

    public function create()
    {
        $categories = CategoryRepository::make()->query()->orderByAsc('sort_order')->get();

        return $this->render('admin.pages.products.templates.create', [
            'categories' => $categories,
        ]);
    }

    public function edit(string $id)
    {
        $template = $this->repo()->with([FileAttachment::class, 'category'])->findOrFail($id);
        $attachments = is_array($template->fileattachment) ? $template->fileattachment : [$template->fileattachment];
        $template->setRelation(FileAttachment::alias(), FileAttachmentList::make($attachments));

        $categories = CategoryRepository::make()->query()->orderByAsc('sort_order')->get();

        return $this->render('admin.pages.products.templates.edit', [
            'template' => $template,
            'categories' => $categories,
        ]);
    }

    protected function uploadConfig(): array
    {
        return [
            'main_image' => 'uploaded|uploadedOk'
        ];
    }

    public function store(SaveProductTemplateRequest $request)
    {
        return $this->runInTransaction(function () use ($request) {
            $productTemplate = new ProductTemplate($request->safe());

            if (!$request->body('category_id')) {
                $productTemplate->category_id = null; 
            }

            $productTemplate = $this->repo()->save($productTemplate);
            $this->fileRepo()->handleUpload($productTemplate, $this->uploadConfig());

            return $this->render(null, [
                'template' => $productTemplate->toArray(), 
                'redirect' => route('admin.product_templates.edit', ['id' => $productTemplate->id])
            ], '제품 템플릿이 생성되었습니다.');
        });
    }

    public function update(string $id, SaveProductTemplateRequest $request)
    {
        return $this->runInTransaction(function () use ($id, $request) {
            $template = $this->repo()->findOrFail($id);
            $template->fill($request->all());

            if (!$request->body('category_id')) {
                $template->category_id = null; 
            }

            $this->repo()->save($template);
            $this->fileRepo()->handleDelete($template);
            $this->fileRepo()->handleUpload($template, $this->uploadConfig());

            return $this->render(null, ['template' => $template], '제품 템플릿이 수정되었습니다.');
        });
    }

    public function destroy(string $id)
    {
        return $this->runInTransaction(function () use ($id) {
            $template = $this->repo()->with([FileAttachment::class])->findOrFail($id);

            $success = $this->repo()->delete($template);

            if (!$success) {
                throw new RuntimeException("제품 템플릿 삭제에 실패했습니다.");
            }

            return $this->render('admin.product_templates.index', [], '제품 템플릿이 삭제되었습니다.');
        });
    }

    public function destroyMany(Request $request)
    {
        return $this->runInTransaction(function () use ($request) {
            $ids = $request->body('ids', []);

            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (empty($ids)) {
                return $this->render(null, [], "삭제할 항목이 없습니다.");
            }

            $deletedCount = 0;

            foreach ($ids as $id) {
                $template = $this->repo()
                    ->with([FileAttachment::class])
                    ->find($id);

                if (!$template) continue;

                $success = $this->repo()->delete($template);
                
                if ($success) $deletedCount++;
            }

            return $this->render(null, [], "선택된 제품 템플릿이 삭제되었습니다. (삭제된 수: {$deletedCount})");
        });
    }

}
