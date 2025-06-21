<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Repositories\CartItemRepository;
use App\Domains\Product\Repositories\ProductRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class CartItemController extends Controller
{
    protected function repo(): CartItemRepository
    {
        return CartItemRepository::make();
    }

    public function update(string $id, Request $request)
    {
        return $this->runInTransaction(function () use ($id, $request) {
            $cartitem = $this->repo()->find($id);

            if (!$cartitem) {
                throw new RuntimeException("아이템을 찾을 수 없습니다.");
            }

            $cartitem->fill($request->all());
            $cartitem = $this->repo()->save($cartitem);

            if (!$cartitem) {
                throw new RuntimeException("아이템 수정 중 문제가 발생하였습니다.");
            }

            return $this->render(null, ['cartitem' => $cartitem->toArray()]);
        });
    }

    public function destroy(string $id, Request $request)
    {
        return $this->runInTransaction(function () use ($id, $request) {
            $cartitem = $this->repo()->find($id);

            if (!$cartitem) {
                throw new RuntimeException("아이템을 찾을 수 없습니다.");
            }

            // CartItem과 관련된 Product 삭제
            $prodRepo = ProductRepository::make();
            $product = $prodRepo->find($cartitem->product_id);
            
            if (!$product) {
                throw new RuntimeException("아이템을 찾을 수 없습니다.");
            }
            
            $deleted = $prodRepo->delete($product); // Product 삭제
            if (!$deleted) {
                throw new RuntimeException("아이템 삭제 중 문제가 발생하였습니다.");
            }

            return $this->render(null, [], "정상적으로 삭제되었습니다.");
        });
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->body('ids', []);
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            return $this->render(null, [], "삭제할 항목이 없습니다.");
        }

        return $this->runInTransaction(function () use ($ids) {
            $deletedCount = 0;

            // 여러 개의 CartItem에 대해 처리
            foreach ($ids as $id) {
                $cartitem = $this->repo()->find($id);

                if ($cartitem) {
                    // 연관된 Product 삭제
                    $prodRepo = ProductRepository::make();
                    $product = $prodRepo->find($cartitem->product_id);

                    if ($product) {
                        $prodRepo->delete($product); // Product 삭제
                    }

                    // CartItem 삭제
                    $this->repo()->delete($cartitem);
                    $deletedCount++;
                }
            }

            return $this->render(null, [], "선택된 아이템들이 삭제되었습니다. (삭제된 수: {$deletedCount})");
        });
    }
}
