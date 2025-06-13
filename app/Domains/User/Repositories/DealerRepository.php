<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\Entities\Dealer;

use Framework\Database\ORM\Repositories\Repository;

class DealerRepository extends Repository
{
    public static function table(): string
    {
        return 'dealers';
    }

    public static function entityClass(): string
    {
        return Dealer::class;
    }

}
