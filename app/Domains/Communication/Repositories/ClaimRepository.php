<?php

namespace App\Domains\Communication\Repositories;

use App\Domains\Communication\Entities\Claim;
use Framework\Database\ORM\Repositories\Repository;


class ClaimRepository extends Repository
{
    public static function table(): string
    {
        return 'claims';
    }

    public static function entityClass(): string
    {
        return Claim::class;
    }
}