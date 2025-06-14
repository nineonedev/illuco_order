<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\Entities\User;
use Framework\Database\ORM\Repositories\Repository;

class UserRepository extends Repository
{
    public static function table(): string
    {
        return 'users';
    }

    public static function entityClass(): string
    {
        return User::class;
    }
}
