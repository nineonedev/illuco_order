<?php

namespace App\Domains\Product\Repositories;

use App\Domains\Product\Entities\ProductOption;
use Framework\Database\ORM\Repositories\Repository;

class ProductOptionRepository extends Repository
{
    public static function table(): string
    {
        return 'product_options';
    }

    public static function entityClass(): string
    {
        return ProductOption::class;
    }
}
