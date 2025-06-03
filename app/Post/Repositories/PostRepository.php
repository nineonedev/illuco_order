<?php 

namespace App\Post\Repositories;

use App\Post\Entities\Post;
use Framework\Database\Repositories\Repository;

class PostRepository extends Repository
{
    protected $table = "posts";
    protected $entityClass = Post::class;
}