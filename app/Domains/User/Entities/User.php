<?php

namespace App\Domains\User\Entities;

use App\Domains\User\Repositories\UserRepository;
use Framework\Database\ORM\Entities\MorphEntity;
use Framework\Security\Auth\Providers\AuthenticatableInterface;

class User extends MorphEntity implements  AuthenticatableInterface
{
    protected array $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    public static function morphType(): string
    {
        return 'userable';
    }
    
    public static function table(): string
    {
        return 'users';
    }

    public static function repositoryClass(): string
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
