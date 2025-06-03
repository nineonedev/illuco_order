<?php

namespace App\User\Entities;

use App\Post\Entities\Post;
use App\User\Repositories\UserRepository;
use Framework\Database\Entities\Entity;
use Framework\Database\Relations\HasMany;

class User extends Entity
{   protected $fillable = [
        'username', 
        'name', 
        'password'
    ];

    protected $repositoryClass = UserRepository::class;

    public function posts(): HasMany
    {
        return $this->hasMany(
            Post::class, 
            'user_id'
        ); 
    }
}