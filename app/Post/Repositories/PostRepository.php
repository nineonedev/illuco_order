<?php 

namespace App\Post\Repositories;

use Framework\Database\Model\Repositories\Repository;

class PostRepository extends Repository
{
    protected string $table = 'posts';
}
