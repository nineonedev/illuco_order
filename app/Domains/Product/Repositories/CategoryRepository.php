<?php

namespace App\Domains\Product\Repositories;

use App\Domains\Product\Entities\Category;
use Framework\Database\ORM\Repositories\Repository;

class CategoryRepository extends Repository
{
    public static function table(): string
    {
        return 'product_categories';
    }

    public static function entityClass(): string
    {
        return Category::class;
    }
}
