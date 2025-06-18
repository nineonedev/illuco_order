<?php

namespace App\Domains\Product\Repositories;

use App\Domains\Product\Entities\ProductAttribute;
use Framework\Database\ORM\Repositories\Repository;

class ProductAttributeRepository extends Repository
{
    public static function table(): string
    {
        return 'product_attributes';
    }

    public static function entityClass(): string
    {
        return ProductAttribute::class;
    }
}
