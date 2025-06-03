<?php 

namespace App\User\Repositories;

use App\User\Entities\User;
use App\User\Observers\HashPasswordObserver;
use Framework\Database\Repositories\Repository;

class UserRepository extends Repository
{
    protected $table = "users";
    protected $entityClass = User::class; 

    protected $observers = [
        self::BEFORE_CREATE => [
            HashPasswordObserver::class
        ]
    ];
}