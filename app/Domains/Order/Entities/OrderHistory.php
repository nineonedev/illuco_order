<?php

namespace App\Domains\Order\Entities;

use App\Domains\Order\Repositories\OrderHistoryRepository;
use Framework\Database\ORM\Entities\Entity;

class OrderHistory extends Entity
{
    protected array $fillable = [
        'order_id',
        'dealer_id',
        'customer_id',
        'created_by',
        'balance',
        'settled',
        'memo',
    ];

    protected array $casts = [
        'order_id'     => 'int',
        'dealer_id'    => 'int',
        'customer_id'  => 'int',
        'created_by'   => 'int',
        'balance'      => 'decimal',
        'settled'      => 'bool',
        'memo'         => 'string',
    ];

    public static function repositoryClass(): string
    {
        return OrderHistoryRepository::class;
    }
}
