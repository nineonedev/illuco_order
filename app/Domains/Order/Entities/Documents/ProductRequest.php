<?php

namespace App\Domains\Order\Entities\Documents;

use App\Domains\Order\Repositories\Documents\ProductRequestRepository;
use Framework\Database\ORM\Entities\Entity;

class ProductRequest extends Entity
{
    protected array $fillable = [
        'id',
        'product_code',
        'product_name',
        'product_model',
        'box_size',
        'memo',
        'quantity',
        'total_qty',
        'remarks',
    ];

    protected array $casts = [
        'id'             => 'int',
        'product_code'   => 'string',
        'product_name'   => 'string',
        'product_model'  => 'string',
        'box_size'       => 'string',
        'memo'           => 'string',
        'quantity'       => 'int',
        'total_qty'      => 'int',
        'remarks'        => 'string',
    ];

    public static function repositoryClass(): string
    {
        return ProductRequestRepository::class;
    }

    public static function alias(): string
    {
        return 'product_request';
    }

    public static function code(): string
    {
        return 'PR';
    }
}
