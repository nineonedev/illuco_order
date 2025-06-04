<?php

namespace App\User\Models;

use App\Post\Models\Post;
use App\User\Entities\UserEntity;
use App\User\Repositories\UserRepository;
use Framework\Database\Model\Model;
use Framework\Database\Model\Relations\HasMany;

class User extends Model
{
    protected string $entityClass = UserEntity::class;
    protected string $repositoryClass = UserRepository::class;

    public function posts(): HasMany
    {
        return $this->hasMany(
            Post::class, 
            'user_id', 
            'id'
        );
    }
}
