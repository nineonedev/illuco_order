<?php 

namespace App\User\Repositories;

use Framework\Database\Model\Repositories\Repository;

class UserRepository extends Repository
{
    protected string $table = 'users';
}
