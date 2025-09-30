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

    public function update(string $id, Request $request)
    {
        return $this->runInTransaction(function () use ($id, $request) {

            /** @var \App\Domains\Order\Entities\OrderItem $item */
            $item = OrderItemRepository::make()
                ->with(['order'])
                ->findOrFail($id);

            $order = $item->order;

            // ✅ 편집 가능 상태 확인 (NEW/CONFIRMED/PREPARING 허용)
            if (method_exists($order, 'isEditable') && !$order->isEditable()) {
                throw new RuntimeException('출하/완료된 주문은 수정할 수 없습니다.');
            }

            // 1) 유효성: 수량만 갱신
            $request->validateOrFail([
                'quantity' => 'required|integer|min:1|max:10000',
            ]);

            $oldQty = (int)$item->quantity;
            $newQty = (int)$request->body('quantity');

            if ($oldQty === $newQty) {
                // 변화 없으면 합계만 보정 후 반환
                $item->total_price = (float)$item->unit_price * (int)$item->quantity;
                OrderItemRepository::make()->save($item);
                $this->recalcOrderTotal($order);

                return $this->render(null, [
                    'item'  => $item->toArray(),
                    'order' => $order->toArray(),
                ], '변경 사항이 없습니다.');
            }

            // 2) 메인 아이템 합계 재계산
            $item->quantity    = $newQty;
            $item->total_price = (float)$item->unit_price * $newQty;

            $saved = OrderItemRepository::make()->save($item);
            if (!$saved) {
                throw new RuntimeException('주문 아이템 수정에 실패하였습니다.');
            }

            // 3) 메인 아이템이면, 같은 set_group_id의 세트 아이템들도 수량/합계 동기화
            if ((int)$item->is_main_item === 1 && $item->set_group_id) {
                $this->syncSetGroupByMainQuantity($item, $oldQty, $newQty);
            }

            // 4) 주문 합계 재계산
            $this->recalcOrderTotal($order);

            return $this->render(
                null,
                [
                    'item'  => $item->toArray(),
                    'order' => $order->toArray(),
                ],
                '주문 아이템이 수정되었습니다.'
            );
        });
    }

    /** 주문 합계 재계산 */
    private function recalcOrderTotal($order): void
    {
        $items = OrderItemRepository::make()
            ->query()
            ->where('order_id', $order->id)
            ->get();

        $total = 0.0;

        foreach ($items as $it) {
            $calc = (float)$it->unit_price * (int)$it->quantity;
            if ((float)$it->total_price !== $calc) {
                $it->total_price = $calc;
                OrderItemRepository::make()->save($it);
            }
            $total += $calc;
        }

        $order->total_amount = $total;
        OrderRepository::make()->save($order);
    }


    public function destroy(string $id, Request $request)
    {
        return $this->runInTransaction(function () use ($id) {

            /** @var \App\Domains\Order\Entities\OrderItem $orderItem */
            $orderItem = OrderItemRepository::make()
                ->with(['order'])
                ->findOrFail($id);

            $order = $orderItem->order;

            // ✅ 편집 가능 상태 확인 (NEW/CONFIRMED/PREPARING 허용)
            if (method_exists($order, 'isEditable') && !$order->isEditable()) {
                throw new RuntimeException('출하/완료된 주문은 수정할 수 없습니다.');
            }

            // 삭제 대상 목록 만들기: 메인 아이템이면 동일 set_group_id의 세트 아이템들까지 포함
            $itemsToDelete = [$orderItem];

            if ((int)$orderItem->is_main_item === 1 && $orderItem->set_group_id) {
                $children = OrderItemRepository::make()
                    ->query()
                    ->where('order_id', $orderItem->order_id)
                    ->where('set_group_id', $orderItem->set_group_id)
                    ->where('is_main_item', 0)
                    ->get();

                if (!empty($children)) {
                    // 배열 병합
                    foreach ($children as $child) {
                        $itemsToDelete[] = $child;
                    }
                }
            }

            // 관련 CartItem 삭제 + OrderItem 삭제
            foreach ($itemsToDelete as $it) {
                // 연관된 CartItem 제거 (존재 시)
                if (!empty($order->cart_id)) {
                    $cartItem = CartItemRepository::make()
                        ->query()
                        ->where('product_id', $it->product_id)
                        ->where('cart_id', $order->cart_id)
                        ->first();

                    if ($cartItem) {
                        CartItemRepository::make()->delete($cartItem);
                    }
                }

                // 주문 아이템 삭제
                $deleted = OrderItemRepository::make()->delete($it);
                if (!$deleted) {
                    throw new RuntimeException("주문 아이템 삭제 중 문제가 발생하였습니다. (ID: {$it->id})");
                }
            }

            // 주문 합계 재계산 (삭제 후)
            $this->recalcOrderTotal($order);

            return $this->render(null, [], "주문 아이템이 삭제되었습니다.");
        });
    }


    /**
     * 메인 아이템 수량 변경에 따라 같은 set_group_id의 세트 아이템 수량/합계를 동기화
     *
     * @param \App\Domains\Order\Entities\OrderItem $mainItem  변경된 메인 아이템
     * @param int $oldMainQty  변경 전 메인 수량
     * @param int $newMainQty  변경 후 메인 수량
     */
    private function syncSetGroupByMainQuantity(OrderItem $mainItem, int $oldMainQty, int $newMainQty): void
    {
        if ($oldMainQty <= 0 || !$mainItem->set_group_id) {
            return;
        }

        $children = OrderItemRepository::make()
            ->query()
            ->where('order_id', $mainItem->order_id)
            ->where('set_group_id', $mainItem->set_group_id)
            ->where('is_main_item', 0)
            ->get();

        foreach ($children as $child) {
            // 세트 기본수량 역산 (정수 보정)
            $basePerMain = (int)floor(((int)$child->quantity) / $oldMainQty);
            if ($basePerMain < 1) {
                $basePerMain = 1; // 안전장치
            }

            $newChildQty      = $basePerMain * $newMainQty;
            $child->quantity  = $newChildQty;
            $child->total_price = (float)$child->unit_price * $newChildQty;

            OrderItemRepository::make()->save($child);
        }
    }

}
