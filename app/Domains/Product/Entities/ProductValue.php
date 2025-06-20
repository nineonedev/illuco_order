<?php 

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\ProductValueRepository;
use Framework\Database\ORM\Entities\Entity;

class ProductValue extends Entity
{
    protected array $fillable = [
        'product_id',
        'attribute_id',
        'value',
    ];

    protected array $casts = [
        'product_id' => 'int',
        'attribute_id' => 'int',
    ];

    public static function repositoryClass(): string
    {
        return ProductValueRepository::class;
    }
}
