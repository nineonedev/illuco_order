<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\Entities\CartItem;
use App\Domains\Order\Repositories\CartItemRepository;
use App\Domains\Product\Repositories\ProductRepository;
use Exception;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class CartItemController extends Controller
{
    protected function repo(): CartItemRepository
    {
        return CartItemRepository::make();
    }

    public function show(string $id, Request $request)
    {
        return $this->runInTransaction(function () use ($id) {
            $relations = [
                'product.template' => [
                    'fileattachment',
                    'category',
                ]
            ];

            /** @var CartItem|null $cartitem */
            $cartitem = $this->repo()->with($relations)->find($id);

            if (!$cartitem) {
                throw new RuntimeException("아이템을 찾을 수 없습니다.");
            }

            if ($cartitem->product->type) {
                $cartitem->load(['product.' . $cartitem->product->type]);
            }

            if ($cartitem->set_group_id) {
                $subItems = CartItemRepository::make()
                    ->query()
                    ->with($relations)
                    ->where('set_group_id', $cartitem->set_group_id)
                    ->where('is_main_item', false)
                    ->orderByAsc('set_group_sort')
                    ->get();

                $cartitem->setRelation('sets', $subItems);
            } else {
                $cartitem->setRelation('sets', []);
            }

            return $this->render(null, [
                'cartitem' => $cartitem->toArray(),
            ]);
        });
    }

    
    public function updateMany(Request $request)
    {
        $items = $request->body('items', []);

        if (empty($items)) {
            return $this->render(null, [], "수정할 항목이 없습니다.");
        }

        return $this->runInTransaction(function () use ($items) {
            $updatedCount = 0;
            $allUpdatedItems = [];

            foreach ($items as $itemData) {
                if (!isset($itemData['id'])) {
                    continue;
                }

                $id = $itemData['id'];
                $cartItem = $this->repo()->find($id);

                if (!$cartItem) {
                    logger()->warning("updateMany: CartItem not found", [
                        'id' => $id,
                    ]);
                    continue;
                }

                $relations = ['product.template' => [
                    'fileattachment',
                    'category',
                ]];
                $cartItem->fill($itemData);
                $cartItem = $this->repo()->save($cartItem);
                $cartItem->load($relations);

                if (!$cartItem) {
                    logger()->error("updateMany: Failed to update CartItem", [
                        'id' => $id,
                    ]);
                    continue;
                }

                $updatedCount++;

                if ($cartItem->set_group_id) {
                    $subItems = $this->repo()->query()
                        ->with($relations)
                        ->where('set_group_id', $cartItem->set_group_id)
                        ->where('is_main_item', false)
                        ->get();
                
                    $groupedItems = CartItem::groupBySet(
                        array_merge([$cartItem], $subItems)
                    );

                    foreach ($groupedItems as $grouped) {
                        $allUpdatedItems[] = $grouped->toArray();
                    }
                } else {
                    $allUpdatedItems[] = $cartItem->toArray();
                }
            }

            return $this->render(null, [
                'cartitems' => $allUpdatedItems,
            ], "선택된 아이템들이 수정되었습니다. (수정된 수: {$updatedCount})");
        });
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
        return $this->runInTransaction(function () use ($id) {
            $deletedCount = $this->deleteCartItemAndRelated($id);

            if ($deletedCount === 0) {
                throw new RuntimeException("아이템을 찾을 수 없습니다.");
            }

            return $this->render(null, [], "정상적으로 삭제되었습니다. (삭제된 수: {$deletedCount})");
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
            $totalDeleted = 0;

            foreach ($ids as $id) {
                $totalDeleted += $this->deleteCartItemAndRelated($id);
            }

            return $this->render(null, [], "선택된 아이템들이 삭제되었습니다. (삭제된 수: {$totalDeleted})");
        });
    }

    /**
     * 공통 삭제 로직
     *
     * @param string $id
     * @return int 삭제된 CartItem 수
     */
    protected function deleteCartItemAndRelated(string $id): int
    {
        $deletedCount = 0;

        $repo = $this->repo();

        /** @var CartItem|null $cartItem */
        $cartItem = $repo->with(['product'])->find($id);

        if (!$cartItem) {
            return 0;
        }

        $setGroupId = $cartItem->set_group_id;

        $itemsToDelete = [];

        if ($setGroupId) {
            // 그룹 전체 조회
            // $query = $repo->query()
            //     ->with(['product'])
            //     ->where('set_group_id', $setGroupId);

            // [$sql, $bindings] = $query->getGrammar()->compileSelect($query);
            // dump($sql, $bindings);

            // 기존 리포지토리의 builder에 id 가 매핑되어있음! 위에서 id로 find해서 그런거같음. 따라서 새로운 리포지토리 인스턴스 필요!

            $itemsToDelete = CartItemRepository::make()
                ->query()
                ->with(['product'])
                ->where('set_group_id', $setGroupId)
                ->get();
        } else {
            $itemsToDelete[] = $cartItem;
        }

        foreach ($itemsToDelete as $item) {
            $product = $item->product;

            if ($product) {
                ProductRepository::make()->forceDelete($product);
                $deletedCount++;
            }
        }

        return $deletedCount;
    }
}
