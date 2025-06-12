<?php

namespace App\Domains\User\Entities;

use App\Domains\User\Repositories\UserRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Entities\HasWorkDirectory;
use Framework\Database\ORM\Entities\Morphable;
use Framework\Database\ORM\Entities\Permissionable;
use Framework\Security\Auth\Providers\AuthenticatableInterface;

class User extends Entity implements 
    HasWorkDirectory, 
    Morphable, 
    AuthenticatableInterface,
    Permissionable
{
    protected array $fillable = [
        'name',
        'email',
        'password',
        'username',
    ];

    public static function workDirectory(): string
    {
        return 'users';
    }

    public static function permissionType(): string
    {
        return 'user';
    } 

    public static function morphType(): string
    {
        return 'user';
    }

    public function repositoryClass(): string
    {
        return UserRepository::class;
    }

    public function getAuthIdentifier()
    {
        return $this->id; 
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }
}
