<?php

namespace App\Domains\Auth\Entities;

use App\Domains\Auth\Repositories\RoleRepository;
use Framework\Database\ORM\Entities\Entity;

class Role extends Entity
{
    protected array $fillable = [
        'name',
        'label',
        'description',
    ];

    public static function repositoryClass(): string
    {
        return RoleRepository::class;
    }
}