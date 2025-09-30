<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Entities\OrderItem;
use App\Domains\Order\Repositories\CartItemRepository;
use App\Domains\Order\Repositories\OrderItemRepository;
use App\Domains\Order\Repositories\OrderRepository;
use App\Domains\Product\Entities\Headlight;
use App\Domains\Product\Entities\Loupe;
use App\Domains\Product\Entities\Product;
use App\Domains\Product\Repositories\ProductRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Framework\Validation\Validator;
use Framework\Support\Str;
use RuntimeException;

class OrderItemController extends Controller
{
    public function store(Request $request): array
    {
        // 트랜잭션이 있다면 감싸주세요: return $this->runInTransaction(fn() => {...});
        $payload   = $request->all();
        $orderId   = (int)($payload['order_id'] ?? 0);

        /** @var \App\Domains\Order\Entities\Order $order */
        $order = OrderRepository::make()->findOrFail($orderId);
        if (method_exists($order, 'isFinalized') && $order->isFinalized()) {
            throw new RuntimeException('이미 진행된 주문은 수정할 수 없습니다.');
        }

        // 숫자 문자열 -> 소수 2자리 float
        $money = static function ($v): float {
            if ($v === null || $v === '') return 0.0;
            return round((float)$v, 2);
        };

        // ===== 입력 뽑기 / 기본값 =====
        $productData = (array)($payload['product'] ?? []);
        $quantity    = max(1, (int)($payload['quantity'] ?? 1));
        $sets        = (array)($payload['sets'] ?? []);
        $unitPriceIn = $payload['unit_price'] ?? null; // 옵션
        $setGroupId  = $sets ? Str::uuid() : null;

        // ===== product 필수 검증 가볍게 (필요시 Validator로 강화 가능) =====
        if (empty($productData['template_id']) || empty($productData['name'])) {
            throw new RuntimeException('제품 선택은 필수입니다.');
        }

        // ===== type 추론 (빈 문자열 대응) =====
        $type = trim((string)($productData['type'] ?? ''));
        if ($type === '') {
            if (!empty($payload[Loupe::alias()] ?? [])) {
                $type = Loupe::alias();
            } elseif (!empty($payload[Headlight::alias()] ?? [])) {
                $type = Headlight::alias();
            } else {
                $type = null;
            }
        }

        // ===== Product 생성 =====
        $product = new Product([
            'template_id' => (int)$productData['template_id'],
            'name'        => (string)$productData['name'],
            'type'        => $type,
            'code'        => $productData['code']        ?? null,
            'model'       => $productData['model']       ?? null,
            'price'       => $money($productData['price'] ?? 0),
            'description' => $productData['description'] ?? null,
        ]);
        $product = ProductRepository::make()->save($product);
        if (!$product) {
            throw new RuntimeException('제품 생성에 실패하였습니다.');
        }

        // ===== 서브엔티티(loupe/headlight) 생성 (상위/중첩 모두 지원) =====
        if ($type) {
            $subTop    = (array)($payload[$type] ?? []);            // loupe[...]/headlight[...]
            $subNested = (array)($productData[$type] ?? []);        // product[loupe]/product[headlight]
            $subData   = array_merge($subTop, $subNested);

            $subClass = $type === Loupe::alias() ? Loupe::class
                    : ($type === Headlight::alias() ? Headlight::class : null);

            if ($subClass) {
                $useEngraving = (bool)($subData['use_engraving'] ?? false);
                if (!$useEngraving) {
                    $subData['engraving_text'] = null;
                } else {
                    // 각인 시 1개만
                    $quantity = 1;
                }

                $subData['id'] = $product->id; // TPT
                $repo = $subClass::repositoryClass()::make();
                if (!$repo->save(new $subClass($subData))) {
                    throw new RuntimeException('제품 확장에 실패하였습니다.');
                }
            }
        }

        // ===== 메인 OrderItem 생성 (단가/합계 세팅 필수) =====
        $unitPrice = $unitPriceIn !== null && $unitPriceIn !== '' ? $money($unitPriceIn) : (float)$product->price;
        $total     = $unitPrice * $quantity;

        $orderItem = new OrderItem([
            'order_id'       => $order->id,
            'product_id'     => $product->id,
            'quantity'       => $quantity,
            'unit_price'     => $unitPrice,
            'total_price'    => $total,
            'is_main_item'   => true,
            'set_group_id'   => $setGroupId,
            'set_group_sort' => $payload['set_group_sort'] ?? null,
        ]);
        $orderItem = OrderItemRepository::make()->save($orderItem);
        if (!$orderItem) {
            logger()->error('주문 아이템 추가 실패', [
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => $quantity,
            ]);
            throw new RuntimeException('주문 아이템 추가에 실패하였습니다.');
        }
        $orderItem->setRelation('product', $product);

        // ===== 세트 처리: 세트도 Product -> OrderItem, 수량은 (세트수량 * 메인수량) =====
        $setOrderItems = [];
        foreach ($sets as $index => $row) {
            $row = (array)$row;
            $setProdData = (array)($row['product'] ?? []);
            if (empty($setProdData)) {
                throw new RuntimeException("sets[$index].product 데이터가 필요합니다.");
            }

            $setType = trim((string)($setProdData['type'] ?? '')) ?: null;

            $setProduct = new Product([
                'template_id' => (int)$setProdData['template_id'],
                'name'        => (string)$setProdData['name'],
                'type'        => $setType,
                'code'        => $setProdData['code']        ?? null,
                'model'       => $setProdData['model']       ?? null,
                'price'       => $money($setProdData['price'] ?? 0),
                'description' => $setProdData['description'] ?? null,
            ]);
            $setProduct = ProductRepository::make()->save($setProduct);
            if (!$setProduct) {
                throw new RuntimeException("세트 제품 생성에 실패하였습니다. (index: {$index})");
            }

            // 세트 서브엔티티 (있을 때)
            if ($setType) {
                $setSubTop    = (array)($row[$setType] ?? []);
                $setSubNested = (array)($setProdData[$setType] ?? []);
                $setSubData   = array_merge($setSubTop, $setSubNested);

                $setSubClass = $setType === Loupe::alias() ? Loupe::class
                        : ($setType === Headlight::alias() ? Headlight::class : null);

                if ($setSubClass) {
                    if (!($setSubData['use_engraving'] ?? false)) {
                        $setSubData['engraving_text'] = null;
                    }
                    $setSubData['id'] = $setProduct->id;
                    $repo = $setSubClass::repositoryClass()::make();
                    if (!$repo->save(new $setSubClass($setSubData))) {
                        throw new RuntimeException("세트 제품 확장 저장 실패 (index: {$index})");
                    }
                }
            }

            $setQty   = max(1, (int)($row['quantity'] ?? 1));
            $finalQty = $setQty * $quantity; // 메인수량과 곱
            $setUnit  = isset($row['unit_price']) && $row['unit_price'] !== ''
                ? $money($row['unit_price'])
                : (float)$setProduct->price;
            $setTotal = $setUnit * $finalQty;

            $subOrderItem = new OrderItem([
                'order_id'       => $order->id,
                'product_id'     => $setProduct->id,
                'quantity'       => $finalQty,
                'unit_price'     => $setUnit,
                'total_price'    => $setTotal,
                'is_main_item'   => false,
                'set_group_id'   => $setGroupId,
                'set_group_sort' => $index,
            ]);
            $subOrderItem = OrderItemRepository::make()->save($subOrderItem);
            if (!$subOrderItem) {
                throw new RuntimeException("세트 주문 아이템 추가에 실패하였습니다. (index: {$index})");
            }

            $subOrderItem->setRelation('product', $setProduct);
            $setOrderItems[] = $subOrderItem;
        }

        // 세트 정렬 관계 부여(뷰 편의)
        usort($setOrderItems, fn($a,$b) => ((int)($a->set_group_sort ?? 0)) <=> ((int)($b->set_group_sort ?? 0)));
        $orderItem->setRelation('sets', $setOrderItems);

        // ===== 주문 합계 재계산 =====
        $items = OrderItemRepository::make()->query()->where('order_id', $order->id)->get();
        $totalAmount = array_reduce($items, fn($acc, $it) => $acc + (float)($it->total_price ?? 0), 0.0);
        $order->total_amount = $totalAmount;
        OrderRepository::make()->save($order);

        return [
            'success' => true,
            'message' => '주문 아이템이 추가되었습니다.',
            'data'    => [
                'orderitem' => $orderItem->toArray(),
                'order'     => $order->toArray(),
            ],
        ];
    }

    // public function store(Request $request): array
    // {
    //     $result = $this->runInTransaction(function() use ($request) {
    //         $payload = $request->all(); 
    //         $order_id = $payload['order_id'] ?? null; 

    //         $order = OrderRepository::make()->findOrFail($order_id);

    //         // 기본 변수 추출
    //         $productData = $payload['product'] ?? [];
    //         $quantity    = $payload['quantity'] ?? 1;
    //         $sets = $payload['sets'] ?? [];


    //         // 제품 생성
    //         $type = $productData['type'] ?? null; 
    //         $product = new Product(array_merge($productData, [
    //             'template_id' => $productData['template_id'],
    //             'name' => $productData['name'],
    //             'type' => $productData['type'],
    //             'code' => $productData['code'],
    //             'model' => $productData['model'],
    //             'price' => $productData['price'],
    //             'description' => $productData['description'] ?? null,
    //         ]));
            
    //         $product = ProductRepository::make()->save($product);

    //         if (!$product) {
    //             throw new RuntimeException("제품 생성에 실패하였습니다."); 
    //         }
    //         $product->load(['template.fileattachment']);

    //         // 서브 제품 생성
            
    //         if ($type) {
    //             /** @var \Framework\Database\ORM\Entities\Entity|null $subProductClass */
    //             $subProductClass = null;
    //             $subProductData = $payload[$type];

    //             switch ($type) {
    //                 case Loupe::alias():
    //                     $subProductClass = Loupe::class; 

    //                     $useEngraving = $subProductData['use_engraving'] ?? false;
                        
    //                     if (!$useEngraving) {
    //                         $subProductData['engraving_text'] = null;
    //                     } else {
    //                         $quantity = 1; 
    //                     }

    //                     break; 
    //                 case Headlight::alias():
    //                     $subProductClass = Headlight::class;

    //                     $useEngraving = $subProductData['use_engraving'] ?? false;
                        
    //                     if (!$useEngraving) {
    //                         $subProductData['engraving_text'] = null;
    //                     } else {
    //                         $quantity = 1; 
    //                     }

    //                     break; 
    //             }

    //             if ($subProductClass) {
    //                 $subProductData = array_merge($subProductData, ['id' => $product->id]);
    //                 $subProductEntity = new $subProductClass($subProductData);
                    
    //                 /** @var \Framework\Database\ORM\Repositories\Repository $repo */
    //                 $repo = $subProductClass::repositoryClass()::make();

    //                 $subProduct = $repo->save($subProductEntity);

    //                 if (!$subProduct) {
    //                     throw new RuntimeException("제품 확장에 실패하였습니다.");
    //                 }

    //                 $product->setRelation($type, $subProduct);
    //             }
    //         }


    //         $setGroupId = $sets ? Str::uuid() : null; 

    //         // 카트 아이템 생성
    //         $orderItemData = array_merge([
    //             'order_id'        => $order->id,
    //             'product_id'     => $product->id,
    //             'quantity'       => $quantity,
    //         ], [
    //             'set_group_id' => $setGroupId,
    //             'is_main_item' => true,
    //         ]);
            
    //         $orderItem = new OrderItem($orderItemData);
    //         $orderItem = OrderItemRepository::make()->save($orderItem);
    //         $orderItem->setRelation('product', $product);

    //         if (!$orderItem) {
    //             logger()->error("주문 아이템 추가 실패", [
    //                 'order_id'     => $order->id,
    //                 'product_id'  => $product->id,
    //                 'quantity'    => $quantity,
    //             ]);

    //             throw new RuntimeException("주문 아이템 추가에 실패하였습니다.");
    //         }

    //         if ($sets) { 
    //             $setGroupProducts = []; 

    //             foreach ($sets as $index => $data) {
    //                 $setGroupProduct = new Product($data['product'] ?? []); 
    //                 $setGroupProduct = ProductRepository::make()->save($setGroupProduct);
    //                 $quantity = $data['quantity'] ?? 1;
    //                 $setGroupProduct->load(['template.fileattachment']);

    //                 if (!$setGroupProduct) {
    //                     throw new RuntimeException("세트 생성에 실패하였습니다.");
    //                 }

    //                 $subOrderItemData = [
    //                     'order_id' => $order_id,
    //                     'product_id' => $setGroupProduct->id,
    //                     'quantity' => $quantity,
    //                     'is_main_item' => false, 
    //                     'set_group_id' => $setGroupId, 
    //                     'set_group_sort' => $index,
    //                 ];

                    
    //                 $subOrderItem = new OrderItem($subOrderItemData);
    //                 $subOrderItem = OrderItemRepository::make()->save($subOrderItem);

    //                 if (!$subOrderItem) {
    //                     throw new RuntimeException("주문 아이템 추가에 실패하였습니다.");
    //                 }

    //                 $subOrderItem->setRelation('product', $setGroupProduct);
    //                 $setGroupProducts[] = $subOrderItem;
    //             }

    //             usort($setGroupProducts, function ($a, $b) {
    //                 return ($a->set_group_sort ?? 0) <=> ($b->set_group_sort ?? 0);
    //             });

    //             $orderItem->setRelation('sets', $setGroupProducts);
    //         } else {
    //             $orderItem->setRelation('sets', []);
    //         }

    //         return [
    //             'message' => '기존 오더에 추가되었습니다.',
    //             'data' => [
    //                 'orderitem' => $orderItem->toArray(),
    //             ],
    //         ];
    //     });

    //     return $result->toResponse(); 
    // }


    public function update(string $orderItemId, Request $request)
    {
        return $this->runInTransaction(function () use ($orderItemId, $request) {

            // 1) 대상 아이템 + 주문
            /** @var \App\Domains\Order\Entities\OrderItem $item */
            $item = OrderItemRepository::make()
                ->with(['order'])
                ->findOrFail($orderItemId);

            $order = $item->order;
            if (method_exists($order, 'isFinalized') && $order->isFinalized()) {
                throw new RuntimeException('이미 진행된 주문은 수정할 수 없습니다.');
            }

            // 2) 입력 검증 (단일 아이템 갱신)
            $request->validateOrFail([
                'quantity'       => 'required|integer|min:1|max:9999',
                'unit_price'     => 'nullable|integer|min:0',
                'set_group_id'   => 'nullable|string|maxLength:64',
                'set_group_sort' => 'nullable|integer|min:0|max:9999',
                'is_main_item'   => 'nullable|boolean',
            ]);

            $quantity     = (int)$request->body('quantity');
            $unitPriceIn  = $request->body('unit_price');
            $setGroupId   = $request->body('set_group_id');
            $setGroupSort = $request->body('set_group_sort');
            $isMain       = $request->body('is_main_item');

            // 3) 적용
            $item->quantity       = $quantity;
            // unit_price 미지정 시 기존 값 유지
            if ($unitPriceIn !== null && $unitPriceIn !== '') {
                $item->unit_price = (int)$unitPriceIn;
            }
            $item->total_price    = (int)$item->unit_price * (int)$item->quantity;

            if ($setGroupId !== null && $setGroupId !== '') {
                $item->set_group_id = $setGroupId;
            }
            if ($setGroupSort !== null && $setGroupSort !== '') {
                $item->set_group_sort = (int)$setGroupSort;
            }
            if ($isMain !== null) {
                $item->is_main_item = $isMain ? 1 : 0;
            }

            $saved = OrderItemRepository::make()->save($item);
            if (!$saved) {
                throw new RuntimeException('주문 아이템 수정에 실패하였습니다.');
            }

            // 4) 주문 합계 재계산
            $this->recalcOrderTotal($order);

            return $this->render(null, [
                'item'  => $item->toArray(),
                'order' => $order->toArray(),
            ], '주문 아이템이 수정되었습니다.');
        });
    }

    public function destroy(string $orderItemId, Request $request)
    {
        return $this->runInTransaction(function () use ($orderItemId) {
            // 1. 주문 아이템 조회
            $orderItem = OrderItemRepository::make()
                ->with(['order'])
                ->find($orderItemId);

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
                CartItemRepository::make()->delete($cartItem);
            }

            // 3. 주문 아이템 삭제
            $deleted = OrderItemRepository::make()->delete($orderItem);
            if (!$deleted) {
                throw new RuntimeException("주문 아이템 삭제 중 문제가 발생하였습니다.");
            }

            // 4. 주문 합계 재계산 (삭제 후)
            $this->recalcOrderTotal($orderItem->order);

            return $this->render(null, [], "주문 아이템이 삭제되었습니다.");
        });
    }

    /** 주문 합계 재계산 */
    private function recalcOrderTotal($order): void
    {
        $items = OrderItemRepository::make()
            ->query()
            ->where('order_id', $order->id)
            ->get();

        $total = array_reduce($items, function ($acc, $it) {
            return $acc + (int)($it->total_price ?? 0);
        }, 0);

        $order->total_amount = $total;
        OrderRepository::make()->save($order);
    }
}
