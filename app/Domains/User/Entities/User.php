<?php

namespace App\Domains\User\Entities;

use App\Domains\User\Repositories\UserRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Entities\HasWorkDirectory;
use Framework\Database\ORM\Entities\Morphable;

class User extends Entity implements HasWorkDirectory, Morphable
{
    protected array $fillable = [
        'name',
        'email',
        'password',
        'username',
        'birth',
        'gender',
        'remember_token',
        'locked_at',
        'last_login_at',
        'login_count',
        'login_ip'
    ];

    public static function workDirectory(): string
    {
        return 'users';
    }

    public static function morphType(): string
    {
        return 'user';
    }

    public function repositoryClass(): string
    {
        return UserRepository::class;
    }
}
