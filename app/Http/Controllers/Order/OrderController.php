<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Entities\CartItem;
use App\Domains\Order\Entities\Order;
use App\Domains\Order\Entities\OrderItem;
use App\Domains\Order\Enums\OrderStatus;
use App\Domains\Order\Repositories\CartItemRepository;
use App\Domains\Order\Repositories\CartRepository;
use App\Domains\Order\Repositories\CustomerRepository;
use App\Domains\Order\Repositories\OrderItemRepository;
use App\Domains\Order\Repositories\OrderRepository;
use App\Domains\Product\Entities\Product;
use App\Domains\Product\Repositories\ProductRepository;
use App\Domains\User\Repositories\DealerRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderRepository::make()->with(['customer', 'user'])->query();

        // 대리점인 경우, 대리점에 해당하는 주문만 조회
        if (user()->isDealer()) {
            $dealerId = user()->dealer->id; // 대리점의 ID
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


    public function show(Request $request, int $id)
    {
        $order = OrderRepository::make()
            ->with([
                'customer',
                'user',
                'items.product.template.fileattachment',
            ])
            ->findOrFail($id);

        return $this->render('admin.pages.orders.show', [
            'order' => $order,
        ]);
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

            // 나중에 대리점에서 주문했을때도 고려해야함. 
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

            
            $allSetIds = [];
            
            foreach ($cartitems as $cartitem) {
                $sets = CartItemRepository::make()
                    ->query()
                    ->with(['product'])
                    ->where('is_main_item', false)
                    ->where('set_group_id', $cartitem->set_group_id)
                    ->get();  

                $cartitem->setRelation('sets', $sets);

                if (!empty($cartitem->sets)) {
                    foreach ($sets as $set) {
                        $allSetIds[] = $set->id; 
                    }
                }
            }

            $totalAmount = array_reduce($cartitems, function ($acc, $item) {
                $mainPrice = $item->product->price * $item->quantity;

                $setTotal = 0;
                $qty = $item->quantity;

                if (!empty($item->sets)) {
                    foreach ($item->sets as $setGroupItem) {
                        $setTotal += $setGroupItem->product->price * $setGroupItem->quantity;
                    }
                    $setTotal *= $qty;
                }

                return $acc + $mainPrice + $setTotal;
            }, 0);

            $user = user();

            $dealer = null;

            if ($user->isDealer()) {
                $dealerId = $user->dealer ? $user->dealer->id : null;

                if ($dealerId) {
                    $dealer = DealerRepository::make()->find($dealerId);
                    if (!$dealer) {
                        throw new RuntimeException("등록되지 않은 대리점 정보입니다.");
                    }
                }
            }

            $orderData = [
                'customer_id'    => $customer->id,
                'dealer_id'      => $dealer ? $dealer->id : null,
                'user_id'        => $user->id,
                'orderer_name'   => $user->name,
                'orderer_email'  => $user->email,
                'orderer_phone'  => $user->phone,
                'memo'           => $memo,
                'total_amount'   => $totalAmount,
            ];

            $order = new Order($orderData);
            $order->generateOrderNumber($dealer ? $dealer->code : null);
            $order = OrderRepository::make()->save($order);

            if (!$order) {
                throw new RuntimeException('주문 생성에 실패하였습니다.');
            }

            // order_items 저장
            foreach ($cartitems as $cartitem) {
                // 메인 아이템 저장
                $orderItemData = [
                    'order_id'        => $order->id,
                    'product_id'      => $cartitem->product_id,
                    'quantity'        => $cartitem->quantity,
                    'unit_price'      => $cartitem->product->price,
                    'total_price'     => $cartitem->product->price * $cartitem->quantity,
                    'set_group_id'    => $cartitem->set_group_id,
                    'set_group_sort'  => $cartitem->set_group_sort,
                    'is_main_item'    => $cartitem->is_main_item ? 1 : 0,
                ];

                $orderItem = new OrderItem($orderItemData);
                $orderItem = OrderItemRepository::make()->save($orderItem);

                if (!$orderItem) {
                    throw new RuntimeException("제품 주문에 실패하였습니다.");
                }

                // 세트 아이템도 저장
                if (!empty($cartitem->sets)) {
                    foreach ($cartitem->sets as $setGroupItem) {
                        $orderSetItemData = [
                            'order_id'        => $order->id,
                            'product_id'      => $setGroupItem->product_id,
                            'quantity'        => $setGroupItem->quantity * $cartitem->quantity,
                            'unit_price'      => $setGroupItem->product->price,
                            'total_price'     => $setGroupItem->product->price * $setGroupItem->quantity * $cartitem->quantity,
                            'set_group_id'    => $setGroupItem->set_group_id,
                            'set_group_sort'  => $setGroupItem->set_group_sort,
                            'is_main_item'    => 0,
                        ];

                        $orderSetItem = new OrderItem($orderSetItemData);
                        $orderSetItem = OrderItemRepository::make()->save($orderSetItem);

                        if (!$orderSetItem) {
                            throw new RuntimeException("세트 제품 주문에 실패하였습니다.");
                        }
                    }
                }
            }

            if (!empty($allSetIds)) {
                CartItemRepository::make()->query()->bulkDelete($allSetIds);
            }

            // 장바구니 아이템 삭제
            CartItemRepository::make()->query()->bulkDelete($ids);

            return $this->render(null, [
                'order' => $order->toArray(),
            ], '주문이 생성되었습니다.');
        });
    }


    public function edit(Request $request, int $id)
    {
        $order = OrderRepository::make()
            ->with([
                'customer',
                'user',
                'items.product.template.fileattachment',
            ])
            ->findOrFail($id);

        foreach ($order->items as $item) {
            $type = $item->product->type;
            if ($item->product->type) {
                $item->product->load([$type]);
            }
        };
        $groupedItems = OrderItem::groupBySet($order->items);
        $order->replaceRelation('items', $groupedItems);
        
        return $this->render('admin.pages.orders.edit', [
            'order' => $order,
        ]);
    }

    public function restoreItem(string $orderItemId, Request $request)
    {
        return $this->runInTransaction(function () use ($orderItemId) {
            $orderItem = OrderItemRepository::make()
                ->query()
                ->with([
                    'product.template.category',
                    'order.customer.cart'
                ])
                ->find($orderItemId);

            if (!$orderItem) {
                throw new RuntimeException("주문 아이템을 찾을 수 없습니다.");
            }

            if ($orderItem->order->isFinalized()) {
                throw new RuntimeException("주문이 완료된 상태입니다. 더 이상 수정할 수 없습니다.");
            }

            // customer 없을 경우도 생각! 
            $customer = $orderItem->order->customer;
            $cart = $customer->cart;

            if (!$cart) {
                $cart = CartRepository::make()->query()->firstOrCreate([
                    'customer_id' => $customer->id,
                ]);
            }

            $cartItem = $this->restoreProduct($orderItem, $cart);

            return $this->render(null, [
                'cartitem' => $cartItem->toArray(),
            ], '주문 아이템이 장바구니에 복원되었습니다.');
        });
    }

    public function restoreAll(string $orderId, Request $request)
    {
        return $this->runInTransaction(function () use ($orderId) {
            $order = OrderRepository::make()
                ->with([
                    'items.product.template',
                    'customer.cart'
                ])
                ->find($orderId);

            if (!$order) {
                throw new RuntimeException("주문을 찾을 수 없습니다.");
            }

            $customer = $order->customer;
            $cart = $customer->cart;

            if (!$cart) {
                $cart = CartRepository::make()->query()->firstOrCreate([
                    'customer_id' => $customer->id,
                ]);
            }

            $restoredCartItems = [];
            $groupedItems = OrderItem::groupBySet($order->items);

            foreach ($groupedItems as $orderItem) {
                $cartItem = $this->restoreProduct($orderItem, $cart);
                $restoredCartItems[] = $cartItem->toArray();
            }

            return $this->render(null, [
                'cartitems' => $restoredCartItems,
            ], '모든 주문 아이템이 장바구니로 복원되었습니다.');
        });
    }

    private function restoreProduct(OrderItem $orderItem, $cart)
    {
        $orderedProduct = $orderItem->product;
        $productTemplate = $orderedProduct->template;

        // === 1) 새 Product 생성 ===
        $newProductData = [
            'template_id' => $productTemplate->id,
            'name'        => $productTemplate->name,
            'type'        => $orderedProduct->type,
            'code'        => $productTemplate->code,
            'model'       => $productTemplate->model,
            'price'       => $productTemplate->price,
            'description' => $productTemplate->description,
        ];

        $newProduct = new Product($newProductData);
        $newProduct = ProductRepository::make()->save($newProduct);

        if (!$newProduct) {
            throw new RuntimeException("제품 생성에 실패하였습니다.");
        }

        // === 2) 확장 제품 생성 (TPT 방식) ===
        $type = $orderedProduct->type;
        if ($type) {
            $orderedProduct->load([$type]);
            $subEntity = $orderedProduct->{$type};
            if ($subEntity) {
                $subProductClass = get_class($subEntity);
                $repoClass = $subProductClass::repositoryClass();
                $subProductData = $subEntity->getAttributes();
                $subProductData['id'] = $newProduct->id;

                $subEntityNew = new $subProductClass($subProductData);
                $savedSub = $repoClass::make()->save($subEntityNew);

                if (!$savedSub) {
                    throw new RuntimeException("제품 확장 저장에 실패하였습니다.");
                }
            }
        }

        // === 3) CartItem 생성 ===
        $setGroupId = $orderItem->set_group_id ? $orderItem->set_group_id : null;

        $cartItemData = [
            'cart_id'        => $cart->id,
            'product_id'     => $newProduct->id,
            'quantity'       => $orderItem->quantity,
            'set_group_id'   => $setGroupId,
            'set_group_sort' => $orderItem->set_group_sort,
            'is_main_item'   => $orderItem->is_main_item,
        ];

        $cartItem = new CartItem($cartItemData);
        $cartItem = CartItemRepository::make()->save($cartItem);

        if (!$cartItem) {
            throw new RuntimeException("메인 제품 장바구니 추가에 실패하였습니다.");
        }

        // === 4) 세트 아이템 복원 ===
        if ($orderItem->is_main_item && $setGroupId) {
            // 세트 OrderItem 조회
            $sets = OrderItemRepository::make()
                ->query()
                ->with(['product.template.category'])
                ->where('set_group_id', $setGroupId)
                ->where('is_main_item', false)
                ->get();

            $setCartItems = [];

            foreach ($sets as $index => $setItem) {
                $setOrderedProduct = $setItem->product;
                $setProductTemplate = $setOrderedProduct->template;

                // 세트용 새 Product 생성
                $newSetProductData = [
                    'template_id' => $setProductTemplate->id,
                    'name'        => $setProductTemplate->name,
                    'type'        => $setOrderedProduct->type,
                    'code'        => $setProductTemplate->code,
                    'model'       => $setProductTemplate->model,
                    'price'       => $setProductTemplate->price,
                    'description' => $setProductTemplate->description,
                ];

                $newSetProduct = new Product($newSetProductData);
                $newSetProduct = ProductRepository::make()->save($newSetProduct);
                

                if (!$newSetProduct) {
                    throw new RuntimeException("세트 제품 생성에 실패하였습니다.");
                }

                // 확장 테이블 저장
                $setType = $setOrderedProduct->type;
                if ($setType) {
                    $subSetEntity = $setOrderedProduct->{$setType};
                    if ($subSetEntity) {
                        $subSetProductClass = get_class($subSetEntity);
                        $repoClass = $subSetProductClass::repositoryClass();
                        $subSetProductData = $subSetEntity->getAttributes();
                        $subSetProductData['id'] = $newSetProduct->id;

                        $subSetEntityNew = new $subSetProductClass($subSetProductData);
                        $savedSetSub = $repoClass::make()->save($subSetEntityNew);

                        if (!$savedSetSub) {
                            throw new RuntimeException("세트 제품 확장 저장에 실패하였습니다.");
                        }
                    }
                }

                $setCartItemData = [
                    'cart_id'         => $cart->id,
                    'product_id'      => $newSetProduct->id,
                    'quantity'        => $setItem->quantity,
                    'set_group_id'    => $setGroupId,
                    'set_group_sort'  => $setItem->set_group_sort,
                    'is_main_item'    => false,
                ];

                $setCartItem = new CartItem($setCartItemData);
                $setCartItem = CartItemRepository::make()->save($setCartItem);

                if (!$setCartItem) {
                    throw new RuntimeException("세트 장바구니 아이템 저장에 실패하였습니다.");
                }

                $setCartItem->setRelation('product', $newSetProduct);
                $setCartItems[] = $setCartItem;
            }

            // 세트 정렬
            usort($setCartItems, function ($a, $b) {
                return ($a->set_group_sort ?? 0) <=> ($b->set_group_sort ?? 0);
            });

            $cartItem->setRelation('sets', $setCartItems);
        }

        // === 5) 관계 로딩 ===
        $cartItem->load([
            'product.template.fileattachment',
        ]);

        return $cartItem;
    }


    public function update(Request $request, int $id)
    {
        return $this->runInTransaction(function () use ($request, $id) {
            $order = OrderRepository::make()->findOrFail($id);

            $order->fill([
                'order_status' => $request->body('order_status', $order->order_status),
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
                        case OrderStatus::NEW:
                        case OrderStatus::CONFIRMED:
                            ProductRepository::make()->forceDelete($product);
                            break;
                        case OrderStatus::PREPARING:
                        case OrderStatus::SHIPPED:
                            ProductRepository::make()->softDelete($product);
                            break;
                        default:
                            ProductRepository::make()->softDelete($product);
                            break;
                    }
                }

                OrderItemRepository::make()->delete($item);
            }

            $deleted = OrderRepository::make()->delete($order);

            if (!$deleted) {
                throw new RuntimeException("주문 삭제에 실패했습니다.");
            }

            return $this->responseWith()
                ->redirectRoute('admin.orders.index')
                ->message('주문이 삭제되었습니다.')
                ->withQuery()
                ->send();
        });
    }
}