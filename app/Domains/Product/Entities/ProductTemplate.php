<?php 

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\ProductTemplateRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Traits\SoftDeletes;

class ProductTemplate extends Entity
{
    use SoftDeletes;
    
    protected array $fillable = [
        'category_id',
        'name',
        'code',
        'model',
        'price',
        'sort_order',
        'description',
    ];

    protected array $casts = [
        'category_id' => '?int',  
        'sort_order' => 'int',
        'price' => 'decimal',
    ];

    public static function repositoryClass(): string
    {
        return ProductTemplateRepository::class;
    }
}