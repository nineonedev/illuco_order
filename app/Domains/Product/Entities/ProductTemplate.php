<?php 

namespace App\Domains\Order\Entities;

use App\Domains\Order\Repositories\ProductTemplateRepository;
use Framework\Database\ORM\Entities\Entity;

class ProductTemplate extends Entity
{
    protected array $fillable = [
        'category_id',
        'name',
        'code',
        'sort_order',
        'description',
    ];

    public static function repositoryClass(): string
    {
        return ProductTemplateRepository::class;
    }
}