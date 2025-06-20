<?php 

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\ProductRepository;
use Framework\Database\ORM\Entities\Entity;

class Product extends Entity
{
    protected array $fillable = [
        'template_id',
        'name',
        'code',
        'model',
        'price',
        'option_json',
    ];

    protected array $casts = [
        'template_id' => 'int',
        'price' => 'decimal',
    ];

    public static function repositoryClass(): string
    {
        return ProductRepository::class;
    }
}