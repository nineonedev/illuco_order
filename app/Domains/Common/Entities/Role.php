<?php

namespace App\Domains\Common\Entities;

use App\Domains\Common\Repositories\RoleRepository;
use Framework\Database\ORM\Entities\Entity;

class Role extends Entity
{
    protected array $fillable = [
        'name',
        'description',
    ];

    public static function repositoryClass(): string
    {
        return RoleRepository::class;
    }
}