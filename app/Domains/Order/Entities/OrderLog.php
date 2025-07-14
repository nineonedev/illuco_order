<?php

namespace App\Domains\Order\Entities;

use App\Domains\Order\Repositories\OrderLogRepository;
use Framework\Database\ORM\Entities\Entity;

class OrderLog extends Entity
{
    protected array $fillable = [
        'order_id',
        'user_id',
        'status',
        'previous_status',
        'created_at',
    ];

    protected array $casts = [
        'order_id'    => 'int',
        'user_id'  => 'int',
        'status'  => 'string',
        'previous_status'  => 'string',
    ];

    public static function repositoryClass(): string
    {
        return OrderLogRepository::class;
    }
}
