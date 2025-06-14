<?php

namespace App\Domains\Notice\Repositories;

use App\Domains\Notice\Entities\Notice;
use Framework\Database\ORM\Repositories\Repository;


class NoticeRepository extends Repository
{
   public static function table(): string
    {
        return 'notices';
    }

    public static function entityClass(): string
    {
        return Notice::class;
    }
}