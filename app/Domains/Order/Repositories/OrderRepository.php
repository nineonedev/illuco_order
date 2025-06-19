<?php

namespace App\Domains\Order\Repositories;

use App\Domains\Order\Entities\Order;
use Framework\Database\ORM\Repositories\Repository;

class OrderRepository extends Repository
{
    public static function table(): string
    {
        return 'orders';
    }

    public static function entityClass(): string
    {
        return Order::class;
    }
}
