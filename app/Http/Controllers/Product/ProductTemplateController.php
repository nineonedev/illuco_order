<?php

namespace App\Http\Controllers\Product;

use App\Domains\Product\Entities\Headlight;
use App\Domains\Product\Entities\Loupe;
use App\Domains\Product\Entities\ProductTemplate;
use App\Domains\Product\Repositories\CategoryRepository;
use App\Domains\Product\Repositories\ProductTemplateRepository;
use App\Domains\System\Entities\FileAttachment;
use App\Domains\System\Repositories\FileAttachmentRepository;
use App\Domains\System\Supports\FileAttachmentList;
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

    public function attributes()
    {

        $data = [
            'loupe' => Loupe::MODEL_SPECS,
            'headlight' => Headlight::MODEL_SPECS,
            'countries' => __('system.countries'),
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

    public function index(Request $request)
    {
        $query = $this->repo()
            ->with([FileAttachment::class, 'category'])
            ->query();

        $categoryId = user()->isDealer() && user()->dealer->category_id ? user()->dealer->category_id : null;

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

        $query->when(
            $categoryId,
            fn($q) =>  $q->where('category_id', $categoryId)
        );

        // 정렬
        $sort = $request->query('sort');
        if ($sort) {
            switch ($sort) {
                case 'latest':
                    $query->orderByDesc('created_at');
                    break;
                case 'oldest':
                    $query->orderBy('created_at');
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
        $templates = $request->expectsJson() ? $paginator->toArray() : $paginator;

        // 카테고리 목록도 같이 내려줌
        $categoryQuery = CategoryRepository::make()
            ->query()
            ->orderByAsc('sort_order');

        if ($categoryId) {
            $categoryQuery->where('id', $categoryId);
        }

        $categories = $categoryQuery->get();

        return $this->render('admin.pages.products.templates.index', [
            'templates'   => $templates,
            'query'       => $request->query(),
            'categories'  => request()->expectsJson() ? array_map(fn($c) => $c->toArray(), $categories) : $categories,
            'isDealer' => user()->isDealer(),
            'category_id' => $categoryId,
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
