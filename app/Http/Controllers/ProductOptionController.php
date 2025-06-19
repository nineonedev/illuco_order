<?php

namespace App\Http\Controllers;

use App\Domains\Product\Entities\ProductOption;
use App\Domains\Product\Repositories\ProductOptionRepository;
use App\Http\Requests\Product\SaveOptionRequest;
use Exception;
use Framework\Routing\Controller;
use RuntimeException;


class ProductOptionController extends Controller
{
    public function repo(): ProductOptionRepository
    {
        return ProductOptionRepository::make();
    }

    public function store(SaveOptionRequest $request)
    {
        return $this->runInTransaction(function() use ($request) {
            $data = $request->safe();
            $attr = ProductOption::make($data);
            $attr = $this->repo()->save($attr); 

            if (!$attr) {
                throw new Exception("옵션 생성에 실패하였습니다..");
            }

            return $this->render(null, ['option' => $attr->toArray()], '옵션이 생성되었습니다.');
        });
    }

    public function update(string $id, SaveOptionRequest $request)
    {
        return $this->runInTransaction(function() use ($request, $id) {
            $attr = $this->repo()->find($id);

            if (!$attr) {
                throw new RuntimeException('속성을 찾을 수 없습니다.');
            }

            $attr->fill($request->safe());
            $this->repo()->save($attr);

            return $this->render(null, ['option' => $attr->toArray()], '속성이 수정되었습니다.');
        });
    }


    public function destroy(string $id)
    {
        return $this->runInTransaction(function() use ($id) {
            $attr = $this->repo()->find($id);

            if (!$attr) {
                throw new RuntimeException('옵션을 찾을 수 없습니다.');
            }

            if (!$this->repo()->delete($attr)) {
                throw new RuntimeException("옵션 삭제에 실패했습니다.");
            }

            return $this->render(null, [], '성공적으로 삭제되었습니다.');
        });
    }

}
