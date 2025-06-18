<?php 

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\ProductAttributeRepository;
use Framework\Database\ORM\Entities\Entity;

class ProductAttribute extends Entity
{
    protected array $fillable = [
        'template_id',
        'label',
        'name',
        'type',
        'required',
        'sort_order',
        'description',
    ];

    protected array $casts = [
        'template_id' => 'int',  
        'sort_order' => 'int',
        'required' => 'bool',
    ];

    public static function repositoryClass(): string
    {
        return ProductAttributeRepository::class;
    }
}