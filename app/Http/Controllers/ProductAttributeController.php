<?php

namespace App\Http\Controllers;

use App\Domains\Product\Entities\ProductAttribute;
use App\Domains\Product\Entities\ProductTemplate;
use App\Domains\Product\Repositories\ProductAttributeRepository;
use App\Domains\Product\Repositories\ProductTemplateRepository;
use App\Http\Requests\Product\SaveProductAttributeRequest;
use Exception;
use Framework\Routing\Controller;
use RuntimeException;

class ProductAttributeController extends Controller
{
    public function repo(): ProductAttributeRepository
    {
        return ProductAttributeRepository::make();
    }

    public function show(string $id)
    {
        // with validation and rule, option
        $attr = $this->repo()->with(['options'])->findOrFail($id);
        return $this->render(null, ['attribute' => $attr->toArray()]);
    }

    public function store(SaveProductAttributeRequest $request)
    {
        return $this->runInTransaction(function() use ($request) {
            $data = $request->safe();
            $attr = ProductAttribute::make($data);
            $attr = $this->repo()->save($attr); 

            if (!$attr) {
                throw new Exception("속성 생성에 실패하였습니다..");
            }

            $templateId = $request->input('template_id'); 
            $sortOrder = $request->input('sort_order', 0); 

            $attr->templates()->sync([
                $templateId => ['sort_order' => $sortOrder],
            ]);

            return $this->render(null, ['attribute' => $attr->toArray()], '속성이 생성되었습니다.');
        });
    }

    public function update(string $id, SaveProductAttributeRequest $request)
    {
        return $this->runInTransaction(function() use ($request, $id) {
            $attr = $this->repo()->find($id);

            if (!$attr) {
                throw new RuntimeException('속성을 찾을 수 없습니다.');
            }

            $attr->fill($request->safe());
            $this->repo()->save($attr);

            return $this->render(null, ['attribute' => $attr->toArray()], '속성이 수정되었습니다.');
        });
    }


    public function destroy(string $id)
    {
        return $this->runInTransaction(function() use ($id) {
            $attr = $this->repo()->find($id);

            if (!$attr) {
                throw new RuntimeException('속성을 찾을 수 없습니다.');
            }

            // 연결 해제 (중간 테이블 제거)
            $attr->templates()->detach();

            if (!$this->repo()->delete($attr)) {
                throw new RuntimeException("속성 삭제에 실패했습니다.");
            }

            return $this->render(null, [], '성공적으로 삭제되었습니다.');
        });
    }

}
