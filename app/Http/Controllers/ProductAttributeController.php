<?php

namespace App\Http\Controllers;

use App\Domains\Product\Entities\ProductAttribute;
use App\Domains\Product\Repositories\ProductAttributeRepository;
use App\Http\Requests\Product\SaveProductAttributeRequest;
use Framework\Routing\Controller;

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

            return $this->render(null, ['attribute' => $attr], '속성이 생성되었습니다.');
        });
    }

    public function update(int $id, SaveProductAttributeRequest $request)
    {
        
    }

    public function destroy(int $id)
    {
        
    }
}
