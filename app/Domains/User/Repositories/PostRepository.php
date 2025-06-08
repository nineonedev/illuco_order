<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\Entities\Post;
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