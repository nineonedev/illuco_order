<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\Entities\DealerPrice;

use Framework\Database\ORM\Repositories\Repository;

class DealerPriceRepository extends Repository
{
    public static function table(): string
    {
        return 'dealer_prices';
    }

    public static function entityClass(): string
    {
        return DealerPrice::class;
    }
}
