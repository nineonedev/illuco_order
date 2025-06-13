<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\Entities\Admin;
use App\Domains\User\Observers\AfterCreateUserObserver;
use Framework\Database\ORM\Repositories\Repository;
use Framework\Database\ORM\Repositories\RepositoryEvent;

class AdminRepository extends Repository
{
    public static function table(): string
    {
        return 'admins';
    }

    public static function entityClass(): string
    {
        return Admin::class;
    }

    protected function registerObservers(): void
    {
        $this->on(RepositoryEvent::AFTER_CREATE, AfterCreateUserObserver::class);
    }
}
