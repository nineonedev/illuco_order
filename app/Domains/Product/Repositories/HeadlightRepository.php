<?php

namespace App\Domains\Product\Repositories;

use App\Domains\Product\Entities\Headlight;
use Framework\Database\ORM\Repositories\Repository;

class HeadlightRepository extends Repository
{
    public static function table(): string
    {
        return 'product_headlights';
    }

    public static function entityClass(): string
    {
        return Headlight::class;
    }
}
