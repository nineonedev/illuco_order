<?php

namespace App\Domains\Communication\Repositories;

use App\Domains\Communication\Entities\SalesInfo;
use Framework\Database\ORM\Repositories\Repository;


class SalesInfoRepository extends Repository
{

    public static function table(): string
    {
        return 'sales_info';
    }

    public static function entityClass(): string
    {
        return SalesInfo::class;
    }
}