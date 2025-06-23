<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Entities\CartItem;
use App\Domains\Order\Entities\Order;
use App\Domains\Order\Entities\OrderItem;
use App\Domains\Order\Repositories\CartItemRepository;
use App\Domains\Order\Repositories\CartRepository;
use App\Domains\Order\Repositories\CustomerRepository;
use App\Domains\Order\Repositories\OrderItemRepository;
use App\Domains\Order\Repositories\OrderRepository;
use App\Domains\Product\Entities\Product;
use App\Domains\Product\Entities\ProductValue;
use App\Domains\Product\Repositories\ProductRepository;
use App\Domains\Product\Repositories\ProductValueRepository;
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
        $query = OrderRepository::make()->with(['customer', 'user.userable'])->query();

        // 대리점인 경우, 대리점에 해당하는 주문만 조회
        if (user()->isDealer()) {
            $dealerId = user()->userable->id; // 대리점의 ID
            $query->whereHas('customer', function($q) use ($dealerId) {
                $q->where('dealer_id', $dealerId); // 고객의 dealer_id 필터링
            });
        }

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
            $orderItem = OrderItemRepository::make()
                ->query()
                ->with([
                    'product' => [
                        'template.attributes',
                        'values',
                    ],
                    'order.customer.cart'
                ])
                ->find($orderItemId);

            if (!$orderItem) {
                throw new RuntimeException("주문 아이템을 찾을 수 없습니다. 아이템이 존재하지 않거나 이미 복원된 상태일 수 있습니다.");
            }

            // 주문 상태가 수정 불가능한 경우 처리
            if ($orderItem->order->isFinalized()) {
                throw new RuntimeException("주문이 완료된 상태입니다. 더 이상 수정할 수 없습니다.");
            }

            // 장바구니로 복원
            $customer = $orderItem->order->customer;
            $cart = $customer->cart;

            // 장바구니가 없으면 새로 생성
            if (!$cart) {
                $cart = CartRepository::make()->query()->firstOrCreate(['customer_id' => $customer->id]);
            }

            // 공통된 제품 복원 로직 호출
            $cartItem = $this->restoreProduct($orderItem, $cart);

            // 복원 성공 메시지
            return $this->render(null, ['cartitem' => $cartItem->toArray()], '주문 아이템이 장바구니에 성공적으로 복원되었습니다.');
        });
    }

    // 전체 주문 아이템 복원
    public function restoreAll(string $orderId, Request $request)
    {
        return $this->runInTransaction(function () use ($orderId, $request) {
            // 주문 조회
            $order = OrderRepository::make()->with(['items'])->find($orderId);
            
            if (!$order) {
                throw new RuntimeException("주문을 찾을 수 없습니다.");
            }

            // 모든 주문 아이템을 장바구니로 복원
            foreach ($order->items as $orderItem) {
                // 공통된 제품 복원 로직 호출
                $this->restoreItem($orderItem, $request);
            }

            return $this->render(null, [], '모든 주문 아이템이 장바구니로 복원되었습니다.');
        });
    }

    // 공통된 제품 복원 로직
    private function restoreProduct($orderItem, $cart)
    {
        $orderedProduct = $orderItem->product;
        $productTemplate = $orderedProduct->template;  // 기존 제품의 템플릿을 가져옴
        $attributes = $productTemplate->attributes; // 제품에 관련된 속성들
        $values = $orderedProduct->values;

        
        $normalizedAttributes = [];

        foreach ($attributes as $attribute) {
            $attributeId = $attribute->id;

            $value = null;
            
            foreach ($values as $val) {
                if ($val->attribute_id === $attributeId) {
                    $value = $val->value;
                    break;
                }
            }

            // 기존 값이 있으면
            if ($value) {
                $normalizedAttributes[$attributeId] = [
                    'type' => $attribute->type,
                    'value' => is_array($value) ? array_values($value) : [$value], // 값은 배열로 처리
                ];
            }
        }

        // 새로운 제품 생성
        $newProductData = [
            'template_id' => $productTemplate->id,
            'name' => $productTemplate->name,  // 템플릿에서 name 가져오기
            'code' => $productTemplate->code,  // 템플릿에서 code 가져오기
            'model' => $productTemplate->model, // 템플릿에서 model 가져오기
            'price' => $productTemplate->price, // 템플릿에서 price 가져오기
        ];

        $newProduct = new Product($newProductData);
        $newProduct = ProductRepository::make()->save($newProduct);

        // ProductValue 저장 (속성 타입 포함)
        foreach ($normalizedAttributes as $attributeId => $data) {
            $type = $data['type']; // 속성 타입 (ex: text, multi-select)

            ProductValueRepository::make()->query()
                ->where('product_id', $newProduct->id)
                ->where('attribute_id', $attributeId)
                ->where('attribute_type', $type)
                ->delete();

            foreach ($data['value'] as $value) {
                // ProductValue 저장
                $productValue = ProductValue::make([
                    'product_id'     => $newProduct->id,
                    'attribute_id'   => $attributeId,
                    'attribute_type' => $type,  // 타입 저장
                    'value'          => $value,
                ]);

                ProductValueRepository::make()->save($productValue);
            }
        }

        // 장바구니 아이템 생성
        $cartItem = new CartItem([
            'cart_id' => $cart->id,
            'product_id' => $newProduct->id, // 새로운 제품을 추가
            'quantity' => $orderItem->quantity,
        ]);

        // 카트 아이템 저장
        $saved = CartItemRepository::make()->save($cartItem);

        if (!$saved) {
            throw new RuntimeException("장바구니 아이템 저장 중 문제가 발생했습니다. 다시 시도해 주세요.");
        }

        return $cartItem;
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