<?php 

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\ProductRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Traits\SoftDeletes;

class Product extends Entity
{
    use SoftDeletes;
    
    protected array $fillable = [
        'template_id',
        'name',
        'code',
        'model',
        'price',
        'attribute_json',
    ];

    protected array $casts = [
        'template_id' => 'int',
        'price' => 'decimal',
        'attribute_json' => 'array'
    ];

    public static function repositoryClass(): string
    {
        return ProductRepository::class;
    }
}