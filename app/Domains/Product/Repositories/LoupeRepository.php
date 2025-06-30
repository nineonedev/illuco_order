<?php

namespace App\Domains\Product\Repositories;

use App\Domains\Product\Entities\Loupe;
use Framework\Database\ORM\Repositories\Repository;

class LoupeRepository extends Repository
{
    public static function table(): string
    {
        return 'product_loupes';
    }

    public static function entityClass(): string
    {
        return Loupe::class;
    }
}
