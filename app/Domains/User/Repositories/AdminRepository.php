<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\Entities\Admin;
use Framework\Database\ORM\Repositories\Repository;

class AdminRepository extends Repository
{
    public static function table(): string
    {
        return 'admins';
    }

    public static function entityClass(): string
    {
        return Admin::class;
    }
}
