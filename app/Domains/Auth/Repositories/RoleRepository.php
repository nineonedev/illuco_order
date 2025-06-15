<?php 

namespace App\Domains\Auth\Repositories;

use App\Domains\Auth\Entities\Role;
use Framework\Database\ORM\Repositories\Repository;

class RoleRepository extends Repository
{
    public static function table(): string
    {
        return 'roles';
    }

    public static function entityClass(): string
    {
        return Role::class;
    }
}