<?php

namespace App\User\Entities;

use App\User\Repositories\UserRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Relations\HasMany;

class User extends Entity
{
    protected array $fillable = [
        'name',
        'email',
        'password',
        'is_active'
    ];

    protected array $casts = [
        'is_active' => 'bool'
    ];

    public function repositoryClass(): string
    {
        return UserRepository::class;
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }
}
