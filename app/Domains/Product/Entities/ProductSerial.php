<?php

namespace App\Domains\Product\Entities;

use App\Domains\Order\Entities\OrderItem;
use App\Domains\Product\Repositories\ProductSerialRepository;
use Framework\Database\ORM\Entities\Entity;

class ProductSerial extends Entity
{
    protected array $fillable = [
        'product_id',
        'serial_number',
        'status',
        'order_item_id',
    ];

    protected array $casts = [
        'product_id' => 'int',
        'order_item_id' => 'int',
    ];

    public static function repositoryClass(): string
    {
        return ProductSerialRepository::class;
    }
}
