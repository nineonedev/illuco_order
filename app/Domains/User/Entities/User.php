<?php

namespace App\Domains\User\Entities;

use App\Domains\User\Enums\UserType;
use App\Domains\User\Repositories\UserRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Traits\SoftDeletes;
use Framework\Security\Auth\Providers\AuthenticatableInterface;

class User extends Entity implements AuthenticatableInterface
{
    use SoftDeletes;

    protected array $fillable = [
        'name',
        'type',
        'email',
        'password',
        'phone',
        'gender',
        'email_verified_at',
        'birth',
    ];
    
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

    public function isDealer(): bool
    {
        return $this->user_type === UserType::DEALER; 
    }

    public function isAdmin(): bool
    {
        return $this->user_type === UserType::ADMIN;
    }

    public function isEmployee(): bool
    {
        return $this->user_type === UserType::EMPLOYEE;
    }
}
