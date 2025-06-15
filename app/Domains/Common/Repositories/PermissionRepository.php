<?php 

namespace App\Domains\Common\Repositories;

use App\Domains\Common\Entities\Permission;
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