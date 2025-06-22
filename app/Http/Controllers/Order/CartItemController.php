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

            $request->validateOrFail([
                'quantity' => 'integer',
            ]);

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
            // 1. CartItem 조회
            $cartitem = $this->repo()->find($id);

            if (!$cartitem) {
                throw new RuntimeException("아이템을 찾을 수 없습니다.");
            }

            // 2. CartItem 삭제 (먼저 삭제)
            $cartitemDeleted = $this->repo()->delete($cartitem);
            if (!$cartitemDeleted) {
                throw new RuntimeException("장바구니 아이템 삭제 중 문제가 발생하였습니다.");
            }

            // 3. CartItem에 연결된 Product 삭제
            $prodRepo = ProductRepository::make();
            $product = $prodRepo->find($cartitem->product_id);
            
            if (!$product) {
                throw new RuntimeException("아이템과 연관된 제품을 찾을 수 없습니다.");
            }

            logger()->info("Deleting product with ID: {$product->id}");

            // Product 삭제
            $productDeleted = $prodRepo->delete($product); 

            if (!$productDeleted) {
                logger()->error("제품 삭제 실패", [
                    'product_id' => $product->id,
                ]);
                throw new RuntimeException("제품 삭제에 실패하였습니다.");
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

            // 각 CartItem 삭제 및 연관된 Product 삭제
            foreach ($ids as $id) {
                $cartItem = $this->repo()->find($id);

                if ($cartItem) {
                    // 연관된 Product 찾기
                    $product = ProductRepository::make()->find($cartItem->product_id);

                    if ($product) {
                        ProductRepository::make()->delete($product);
                    }

                    // CartItem 삭제
                    $this->repo()->delete($cartItem);
                    $deletedCount++;
                }
            }

            return $this->render(null, [], "선택된 아이템들이 삭제되었습니다. (삭제된 수: {$deletedCount})");
        });
    }
}
