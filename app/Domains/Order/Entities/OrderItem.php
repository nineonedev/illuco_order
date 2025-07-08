<?php

namespace App\Domains\Order\Entities;

use App\Domains\Order\Repositories\OrderItemRepository;
use Framework\Database\ORM\Entities\Entity;

class OrderItem extends Entity
{
    protected array $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'is_main_item',
        'set_group_id',
        'set_group_sort',
        'box_no',
    ];

    protected array $casts = [
        'order_id'        => 'int',
        'product_id'      => 'int',
        'quantity'        => 'int',
        'unit_price'      => 'decimal',
        'total_price'     => 'decimal',
        'is_main_item'    => 'bool',
        'set_group_id'    => '?string',
        'set_group_sort'  => '?int',
        'box_no'          => '?string',
    ];

    public static function repositoryClass(): string
    {
        return OrderItemRepository::class;
    }

    /**
     * 세트 그룹 아이템들을 메인 기준으로 그룹핑
     *
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

    /**
     * 메인 아이템 여부
     *
     * @return bool
     */
    public function isMain(): bool
    {
        return $this->is_main_item === true;
    }

    /**
     * 세트 아이템 여부
     *
     * @return bool
     */
    public function isSetItem(): bool
    {
        return !$this->isMain() && $this->set_group_id !== null;
    }

    /**
     * 단품(세트 아닌 아이템) 여부
     *
     * @return bool
     */
    public function isSingleItem(): bool
    {
        return !$this->isSetItem() && !$this->isMain();
    }

    /**
     * 총 금액 계산
     *
     * @return float
     */
    public function calculateTotalPrice(): float
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * 세트 아이템들의 총합
     *
     * @return float
     */
    public function getSetTotalPrice(): float
    {
        $total = 0;

        if ($this->isMain() && $this->hasRelation('sets')) {
            foreach ($this->getRelation('sets') as $setItem) {
                $total += $setItem->calculateTotalPrice();
            }
            $total *= $this->quantity;
        }

        return $total;
    }

    /**
     * 세트 아이템 관계가 있는지 여부
     *
     * @return bool
     */
    public function hasSets(): bool
    {
        return $this->hasRelation('sets') && !empty($this->getRelation('sets'));
    }
}
