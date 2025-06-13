<?php 

namespace App\Domains\Common\Entities;

use App\Domains\Common\Repositories\PermissionRepository;
use Framework\Database\ORM\Entities\Entity;

class Permission extends Entity
{
    protected array $fillable = ['resouce', 'action'];

    public static function repositoryClass(): string
    {
        return PermissionRepository::class;
    }
}