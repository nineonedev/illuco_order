<?php

namespace App\Domains\Notice\Repositories;

use App\Domains\Notice\Entities\Notice;
use Framework\Database\ORM\Repositories\Repository;


class NoticeRepository extends Repository
{
   public function table(): string
    {
        return 'notices';
    }

    public function entityClass(): string
    {
        return Notice::class;
    }
}