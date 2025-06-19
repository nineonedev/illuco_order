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
    ];

    protected array $casts = [
        'order_id' => 'int',
        'product_id' => 'int',
        'quantity' => 'int',
    ];

    public static function repositoryClass(): string
    {
        return OrderItemRepository::class;
    }
}
