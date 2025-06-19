<?php 

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\ProductOptionRepository;
use Framework\Database\ORM\Entities\Entity;

class ProductOption extends Entity
{
    protected array $fillable = [
        'label',
        'value',
        'sort_order',
        'attribute_id',
    ];

    protected array $casts = [
        'sort_order' => 'int',
        'attribute_id' => 'int',
    ];

    public static function repositoryClass(): string
    {
        return ProductOptionRepository::class;
    }
}