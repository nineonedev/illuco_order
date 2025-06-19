<?php

namespace App\Domains\Order\Repositories;

use App\Domains\Order\Entities\OrderItem;
use Framework\Database\ORM\Repositories\Repository;

class OrderItemRepository extends Repository
{
    public static function table(): string
    {
        return 'order_items';
    }

    public static function entityClass(): string
    {
        return OrderItem::class;
    }
}
