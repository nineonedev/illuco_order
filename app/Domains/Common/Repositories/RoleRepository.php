<?php 

namespace App\Domains\Common\Repositories;

use App\Domains\Common\Entities\Role;
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