<?php

namespace App\Domains\Claim\Repositories;

use App\Domains\Claim\Entities\Claim;
use Framework\Database\ORM\Repositories\Repository;


class ClaimRepository extends Repository
{
   public function table(): string
    {
        return 'claims';
    }

    public function entityClass(): string
    {
        return Claim::class;
    }
}