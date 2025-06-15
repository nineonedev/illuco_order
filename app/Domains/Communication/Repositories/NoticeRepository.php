<?php

namespace App\Domains\Communication\Repositories;

use App\Domains\Communication\Entities\Notice;
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