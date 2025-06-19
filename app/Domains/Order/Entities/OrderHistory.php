<?php

namespace App\Domains\Order\Entities;

use App\Domains\Order\Repositories\OrderHistoryRepository;
use Framework\Database\ORM\Entities\Entity;

class OrderHistory extends Entity
{
    protected array $fillable = [
        'order_id',
        'user_id',
        'status',
        'description',
    ];

    protected array $casts = [
        'order_id'    => 'int',
        'user_id'     => 'int',
        'status'      => 'string',
        'description' => 'string',
    ];

    public static function repositoryClass(): string
    {
        return OrderHistoryRepository::class;
    }
}
