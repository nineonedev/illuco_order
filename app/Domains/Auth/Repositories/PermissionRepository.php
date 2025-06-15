<?php 

namespace App\Domains\Auth\Repositories;

use App\Domains\Auth\Entities\Permission;
use Framework\Database\ORM\Repositories\Repository;

class PermissionRepository extends Repository
{
    public static function table(): string
    {
        return 'permissions';
    }

    public static function entityClass(): string
    {
        return Permission::class;
    }

}