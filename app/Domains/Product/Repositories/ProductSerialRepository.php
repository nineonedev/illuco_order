<?php

namespace App\Domains\Product\Repositories;

use App\Domains\Product\Entities\ProductSerial;
use Framework\Database\ORM\Repositories\Repository;

class ProductSerialRepository extends Repository
{
    public static function table(): string
    {
        return 'product_serials';
    }

    public static function entityClass(): string
    {
        return ProductSerial::class;
    }
}
