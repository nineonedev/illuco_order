<?php

namespace App\Domains\Product\Repositories;

use App\Domains\Product\Entities\Product;
use Framework\Database\ORM\Repositories\Repository;

class ProductRepository extends Repository
{
    public static function table(): string
    {
        return 'products';
    }

    public static function entityClass(): string
    {
        return Product::class;
    }
}
