<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\Entities\Notice;
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