<?php

namespace App\Domains\Order\Entities;

use App\Domains\Order\Repositories\OrderRepository;
use Framework\Database\ORM\Entities\Entity;

class Order extends Entity
{
    protected array $fillable = [
        'user_id',
        'customer_id',
        'orderer_name',
        'orderer_email',
        'orderer_phone',
        'memo',
        'order_status',
        'total_amount',
    ];

    protected array $casts = [
        'user_id'        => 'int',
        'customer_id'    => 'int',
        'orderer_name'   => 'string',
        'orderer_email'  => 'string',
        'orderer_phone'  => 'string',
        'memo'           => 'string',
        'order_status'   => 'string',
        'total_amount'   => 'int',
    ];

    public static function repositoryClass(): string
    {
        return OrderRepository::class;
    }
}
