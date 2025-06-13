<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\Entities\User;
use App\Domains\User\Observers\BeforeDeleteUserableObserver;
use App\Domains\User\Observers\HashPasswordObserver;
use Framework\Database\ORM\Repositories\Repository;
use Framework\Database\ORM\Repositories\RepositoryEvent;

class UserRepository extends Repository
{
    protected bool $preventsLazyLoading = true;

    public static function table(): string
    {
        return 'users';
    }

    public static function entityClass(): string
    {
        return User::class;
    }

    protected function registerObservers(): void
    {
        $this->on(RepositoryEvent::BEFORE_CREATE, HashPasswordObserver::class);
        $this->on(RepositoryEvent::BEFORE_DELETE, BeforeDeleteUserableObserver::class);
    }

}
