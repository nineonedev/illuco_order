<?php 

namespace App\Domains\Order\Entities;

use App\Domains\Order\Repositories\CartItemRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Support\Collection;

class CartItem extends Entity
{
    protected array $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'is_main_item',
        'set_group_id',
        'set_group_sort',
        'selected',
    ];

    protected array $casts = [
        'cart_id' => 'int',
        'product_id' => 'int',
        'quantity' => 'int',
        'selected' => 'bool',
        'is_main_item' => 'bool',
        'set_group_id' => '?string',
        'set_group_sort' => '?int',
    ];

    public static function repositoryClass(): string
    {
        return CartItemRepository::class;
    }

    public function getAttributeJson(): array
    {
        return $this->product->attribute_json ?? [];
    }

    /**
     * @param Entity[] $items
     * @return array
     */
    public static function groupBySet(array $items): array
    {
        if (empty($items)) {
            return [];
        }

        $updatedItems = [];

        foreach ($items as $item) {
            $addedToGroup = false;

            foreach ($updatedItems as $pushedItem) {
                $isSameGroup = $pushedItem->is_main_item
                    && $item->set_group_id
                    && $item->set_group_id === $pushedItem->set_group_id;

                if ($isSameGroup) {
                    $existing = $pushedItem->getRelation('sets') ?? [];
                    $existing[] = $item;

                    // 정렬
                    usort($existing, function ($a, $b) {
                        return ($a->set_group_sort ?? 0) <=> ($b->set_group_sort ?? 0);
                    });

                    $pushedItem->setRelation('sets', $existing);
                    $addedToGroup = true;
                    break;
                }
            }

            if (!$addedToGroup) {
                $updatedItems[] = $item;
            }
        }

        return $updatedItems;
    }


}
