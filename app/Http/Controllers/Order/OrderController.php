<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Entities\CartItem;
use App\Domains\Order\Entities\Order;
use App\Domains\Order\Entities\OrderItem;
use App\Domains\Order\Repositories\CartItemRepository;
use App\Domains\Order\Repositories\CustomerRepository;
use App\Domains\Order\Repositories\OrderItemRepository;
use App\Domains\Order\Repositories\OrderRepository;
use App\Domains\Product\Entities\Product;
use App\Domains\Product\Repositories\ProductRepository;
use App\Domains\User\Entities\User;
use App\Services\Order\OrderRestoreService;
use Exception;
use Framework\Database\ORM\Entities\EntityCollection;
use Framework\Database\ORM\Rel;
use Framework\Database\Query\EntityQueryBuilder;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderRepository::make()->with(['customer'])->query();

        $query->when($s = $request->query('status'), fn($q) => $q->where('order_status', $s))
            ->when($n = $request->query('name'), fn($q) => 
                $q->where('orderer_name', 'like', "%{$n}%")
                    ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$n}%"))
            )
            ->when($d = $request->query('dealer'), fn($q) => 
                $q->whereHas('customer', fn($cq) => $cq->where('dealer_id', $d))
            )
            ->orderByDesc('id');

        return $this->render('admin.pages.orders.index', [
            'orders' => $query->paginate($request->query('perpage', 15), $request->query('page', 1)),
            'query'  => $request->query(),
        ]);
    }


    public function show()
    {

    }

    public function store(Request $request)
    {
        return $this->runInTransaction(function () use ($request) {
            $ids = $request->body('ids', []);

            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (empty($ids)) {
                throw new RuntimeException("주문할 장바구니 항목이 없습니다.");
            }

            $customer_id = $request->body('customer_id');
            $memo = $request->body('memo', '');

            $customer = CustomerRepository::make()->findOrFail($customer_id);
            $cartitems = CartItemRepository::make()
                ->with(['product'])
                ->query()
                ->findMany($ids);

            if (empty($cartitems)) {
                throw new RuntimeException("선택한 장바구니 항목을 찾을 수 없습니다.");
            }

            $totalAmount = array_reduce($cartitems, function ($acc, $item) {
                return $acc + ($item->product->price * $item->quantity);
            }, 0);

            $user = user();
            $user->load([User::morphType()]);

            $phoneNumber = $user->{User::morphType()}->phone_number ?? null;

            $orderData = [
                'customer_id'    => $customer->id,
                'user_id'        => $user->id,
                'orderer_name'   => $user->name,
                'orderer_email'  => $user->email,
                'orderer_phone'  => $phoneNumber,
                'memo'           => $memo,
                'total_amount'   => $totalAmount,
                'order_status'   => Order::STATUS_RECEIVED,
            ];

            $order = new Order($orderData);
            $order = OrderRepository::make()->save($order);

            // OrderItem 엔티티 저장
            foreach ($cartitems as $cartitem) {
                $orderItem = new OrderItem([
                    'order_id'   => $order->id,
                    'product_id' => $cartitem->product_id,
                    'quantity'   => $cartitem->quantity,
                    'price'      => $cartitem->product->price * $cartitem->quantity,
                ]);

                OrderItemRepository::make()->save($orderItem);
            }

            // 장바구니 항목 제거
            CartItemRepository::make()->query()->bulkDelete($ids);
            
            $remainingCartItems = CartItemRepository::make()
                ->with([
                    'product.values',
                    'product.template' => [
                        'attributes.options',
                        'fileattachment',
                    ],
                ])
                ->query()
                ->whereHas('cart', fn($q) => $q->where('customer_id', $customer->id))
                ->get();

            return $this->render(null, [
                'order' => $order->toArray(),
                'cartitems' =>  EntityCollection::create($remainingCartItems)->toArray(),
            ], '주문이 생성되었습니다.');
        });
    }


    public function edit(Request $request, int $id)
    {
        // dd(CartItemRepository::entityClass());

        $order = OrderRepository::make()
            ->with([
                'customer',
                'user.userable',
                'items.product' => [
                    'values.attribute',
                    'template' => [
                        'attributes.options',
                        'fileattachment'
                    ],
                ]  
            ])
            ->findOrFail($id);

        return $this->render('admin.pages.orders.edit', [
            'order' => $order,
        ]);
    }

    // 단일 주문 아이템 복원
    public function restoreItem(string $orderItemId, Request $request)
    {
        return $this->runInTransaction(function () use ($orderItemId) {
            // 주문 아이템 조회
            $orderItem = OrderRepository::make()
                ->query()
                ->with(['product', 'order'])
                ->find($orderItemId);

            if (!$orderItem) {
                throw new RuntimeException("주문 아이템을 찾을 수 없습니다.");
            }

            if ($orderItem->order->isFinalized()) {
                throw new RuntimeException("주문 상태가 수정 불가능합니다.");
            }

            // 장바구니로 복원
            $cart = $orderItem->order->cart; // 주문에 연결된 장바구니 가져오기
            $cartItem = new CartItem([
                'cart_id' => $cart->id,
                'product_id' => $orderItem->product_id,
                'quantity' => $orderItem->quantity,
            ]);
            CartItemRepository::make()->save($cartItem);

            return $this->render(null, ['cartitem' => $cartItem->toArray()]);
        });
    }

    // 전체 주문 아이템 복원
    public function restoreAll(string $orderId, Request $request)
    {
        return $this->runInTransaction(function () use ($orderId) {
            // 주문 조회
            $order = OrderRepository::make()->find($orderId);
            
            if (!$order) {
                throw new RuntimeException("주문을 찾을 수 없습니다.");
            }

            if ($order->isFinalized()) {
                throw new RuntimeException("주문 아이템 복구가 불가능합니다.");
            }

            // 전체 주문 아이템 복원
            foreach ($order->items as $orderItem) {
                $cart = $order->cart; // 주문에 연결된 장바구니 가져오기
                $cartItem = new CartItem([
                    'cart_id' => $cart->id,
                    'product_id' => $orderItem->product_id,
                    'quantity' => $orderItem->quantity,
                ]);
                CartItemRepository::make()->save($cartItem);
            }

            return $this->render(null, ['message' => '모든 주문 아이템이 장바구니로 복원되었습니다.']);
        });
    }


    public function update(Request $request, int $id)
    {
        return $this->runInTransaction(function () use ($request, $id) {
            $order = OrderRepository::make()->findOrFail($id);

            $order->fill([
                'order_status'  => $request->body('order_status', $order->order_status),
            ]);

            $order = OrderRepository::make()->save($order);

            return $this->render(null, [
                'order' => $order->toArray(),
            ], '주문이 수정되었습니다.');
        });
    }

    public function destroy(Request $request, int $id)
    {
        return $this->runInTransaction(function () use ($id) {
            $order = OrderRepository::make()
                ->with(['items.product'])
                ->findOrFail($id);

            foreach ($order->items as $item) {
                if ($item->product) {
                    $product = $item->product;

                    switch ($order->order_status) {
                        case Order::STATUS_RECEIVED:
                        case Order::STATUS_CONFIRMED:
                            // 아직 생산 전 → 물리 삭제 허용
                            ProductRepository::make()->forceDelete($product);
                            break;

                        case Order::STATUS_PREPARING:
                        case Order::STATUS_SHIPPED:
                            // 생산 이후 → 논리 삭제 처리 (이력 보존 목적)
                            ProductRepository::make()->softDelete($product);
                            break;

                        default:
                            // 보수적 접근: soft delete로 처리
                            ProductRepository::make()->softDelete($product);
                            break;
                    }
                }

                OrderItemRepository::make()->delete($item);
            }


            // 3. Order 삭제
            $deleted = OrderRepository::make()->delete($order);

            if (!$deleted) {
                throw new RuntimeException("주문 삭제에 실패하엿습니다.");
            }

            return $this->responseWith()
                ->redirectRoute('admin.orders.index')
                ->message('주문이 삭제되었습니다.')
                ->withQuery()
                ->send();
        });
    }
}