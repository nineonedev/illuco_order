<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Entities\CartItem;
use App\Domains\Order\Repositories\CartItemRepository;
use App\Domains\Order\Repositories\OrderItemRepository;
use App\Domains\Order\Repositories\OrderRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class OrderItemController extends Controller
{

    public function destroy(string $orderItemId, Request $request)
    {
        return $this->runInTransaction(function () use ($orderItemId) {
            // 1. 주문 아이템 조회
            $orderItem = OrderItemRepository::make()->find($orderItemId);

            if (!$orderItem) {
                throw new RuntimeException("주문 아이템을 찾을 수 없습니다.");
            }

            // 2. 연관된 CartItem 삭제
            $cartItem = CartItemRepository::make()
                ->query()
                ->where('product_id', $orderItem->product_id)
                ->where('cart_id', $orderItem->order->cart_id)
                ->first();

            if ($cartItem) {
                // CartItem 삭제
                CartItemRepository::make()->delete($cartItem);
            }

            // 3. 주문 아이템 삭제
            $deleted = OrderItemRepository::make()->delete($orderItem);

            if (!$deleted) {
                throw new RuntimeException("주문 아이템 삭제 중 문제가 발생하였습니다.");
            }

            return $this->render(null, [], "주문 아이템이 삭제되었습니다.");
        });
    }
}