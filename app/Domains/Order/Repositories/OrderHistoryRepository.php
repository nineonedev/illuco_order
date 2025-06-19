<?php

namespace App\Domains\Order\Repositories;

use App\Domains\Order\Entities\OrderHistory;
use Framework\Database\ORM\Repositories\Repository;

class OrderHistoryRepository extends Repository
{
    public static function table(): string
    {
        return 'order_histories';
    }

    public static function entityClass(): string
    {
        return OrderHistory::class;
    }
}
