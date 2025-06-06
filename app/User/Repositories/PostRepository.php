<?php

namespace App\User\Repositories;

use App\User\Entities\Post;
use Framework\Database\ORM\Relations\BelongsTo;
use Framework\Database\ORM\Repositories\Repository;

class PostRepository extends Repository
{
    public function table(): string
    {
        return 'posts';
    }

    public function entityClass(): string
    {
        return Post::class;
    }
}