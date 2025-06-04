<?php

namespace App\Post\Models;

use App\Post\Entities\PostEntity;
use App\Post\Repositories\PostRepository;
use App\User\Models\User;
use Framework\Database\Model\Model;
use Framework\Database\Model\Relations\BelongsTo;

class Post extends Model
{
    protected string $entityClass = PostEntity::class;
    protected string $repositoryClass = PostRepository::class;

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class, 
            'user_id', 
            'id'
        );
    }
}
