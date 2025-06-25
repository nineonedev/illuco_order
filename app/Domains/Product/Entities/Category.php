<?php 

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\CategoryRepository;
use Framework\Database\ORM\Entities\Entity;

class Category extends Entity
{
    protected array $fillable = [
        'slug',
        'label',
        'sort_order',
        'parent_id',
        'is_locked',
    ];

    protected array $casts = [
        'sort_order' => 'int',
        'parent_id' => 'int',
        'is_locked' => 'bool',
    ];

    public static function repositoryClass(): string
    {
        return CategoryRepository::class;
    }
}