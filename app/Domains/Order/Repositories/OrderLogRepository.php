<?php

namespace App\Domains\Order\Repositories;

use App\Domains\Order\Entities\OrderLog;
use Framework\Database\ORM\Repositories\Repository;

class OrderLogRepository extends Repository
{
    public static function table(): string
    {
        return 'order_logs';
    }

    public static function entityClass(): string
    {
        return OrderLog::class;
    }
}
