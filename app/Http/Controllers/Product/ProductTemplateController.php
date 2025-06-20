<?php

namespace App\Http\Controllers\Product;

use App\Domains\Product\Entities\ProductAttribute;
use App\Domains\Product\Entities\ProductTemplate;
use App\Domains\Product\Repositories\ProductTemplateRepository;
use App\Domains\System\Entities\FileAttachment;
use App\Domains\System\Repositories\FileAttachmentRepository;
use App\Domains\System\Supports\FileAttachmentList;
use App\Http\Requests\Product\SaveProductTemplateRequest;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class ProductTemplateController extends Controller
{
    public function repo(): ProductTemplateRepository
    {
        return ProductTemplateRepository::make();
    }
    public function fileRepo(): FileAttachmentRepository
    {
        return FileAttachmentRepository::make();
    }

    public function index(Request $request)
    {
        $query = $this->repo()->with([FileAttachment::class, 'attributes.options'])->query();

        if ($name = $request->input('name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        $paginator = $query->paginate($request->query('perpage', 15), $request->query('page', 1));
        $templates = $request->expectsJson() ? $paginator->toArray() : $paginator;

        return $this->render('admin.pages.products.templates.index', [
            'templates' => $templates
        ]);
    }

    public function show(string $id, Request $request) 
    {
        $template = $this->repo()->with([FileAttachment::class, 'attributes.options'])->findOrFail($id);

        $template = $request->expectsJson() ? $template->toArray() : $template;
        
        return $this->render('admin.pages.products.templates.show', ['template' => $template]);
    }

    public function create()
    {
        return $this->render('admin.pages.products.templates.create');
    }

    public function edit(string $id)
    {
        $template = $this->repo()->with([FileAttachment::class, 'attributes.options'])->findOrFail($id);
        $attachments = is_array($template->fileattachment) ? $template->fileattachment : [$template->fileattachment];
        

        $template->setRelation(FileAttachment::alias(), FileAttachmentList::make($attachments));

        return $this->render('admin.pages.products.templates.edit', [
            'template' => $template
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
            $this->fileRepo()->deleteAllFor($template);

            if (!$this->repo()->delete($template)) {
                throw new RuntimeException("제품 템플릿 삭제에 실패했습니다.");
            }

            return $this->render('admin.product_templates.index', [], '제품 템플릿이 삭제되었습니다.');
        });
    }
}
