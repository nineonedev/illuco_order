<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\Entities\DealerMemo;

use Framework\Database\ORM\Repositories\Repository;

class DealerMemoRepository extends Repository
{
    public static function table(): string
    {
        return 'dealer_memos';
    }

    public static function entityClass(): string
    {
        return DealerMemo::class;
    }
}
