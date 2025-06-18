<?php

namespace App\Http\Controllers;

use App\Domains\Product\Entities\ProductAttribute;
use App\Domains\Product\Repositories\ProductAttributeRepository;
use App\Http\Requests\Product\SaveProductAttributeRequest;
use Framework\Routing\Controller;
use RuntimeException;

class ProductAttributeController extends Controller
{
    public function repo(): ProductAttributeRepository
    {
        return ProductAttributeRepository::make();
    }
    public function store(SaveProductAttributeRequest $request)
    {
        return $this->runInTransaction(function() use ($request) {
            $data = $request->safe();
            $attr = ProductAttribute::make($data);
            $this->repo()->save($attr); 

            return $this->render(null, ['attribute' => $attr->toArray()], '속성이 생성되었습니다.');
        });
    }

    public function update(int $id, SaveProductAttributeRequest $request)
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

    public function destroy(int $id)
    {
        return $this->runInTransaction(function() use ($id) {
            $attr = $this->repo()->find($id);

            if (!$attr) {
                throw new RuntimeException('속성을 찾을 수 없습니다.');
            }

            if (!$this->repo()->delete($attr)) {
                throw new RuntimeException("속성 삭제에 실패했습니다.");
            }

            return $this->render(null, [], '성공적으로 삭제되었습니다.');
        });
    }
}
