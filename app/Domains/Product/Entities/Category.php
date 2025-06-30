<?php

namespace App\Domains\Product\Entities;

use App\Domains\Product\Repositories\CategoryRepository;
use Framework\Database\ORM\Entities\Entity;

class Category extends Entity
{
    protected array $fillable = [
        'parent_id',
        'slug',
        'label',
        'description',
        'is_visible',
        'sort_order',
    ];

    public static function repositoryClass(): string
    {
        return CategoryRepository::class;
    }
}
