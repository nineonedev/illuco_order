<?php

namespace App\Domains\Product\Repositories;

use App\Domains\Product\Entities\ProductValue;
use Framework\Database\ORM\Repositories\Repository;

class ProductValueRepository extends Repository
{
    public static function table(): string
    {
        return 'product_values';
    }

    public static function entityClass(): string
    {
        return ProductValue::class;
    }
}
