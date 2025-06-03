<?php

namespace App\User\Observers;

use App\User\Entities\User;
use Framework\Support\Facades\Hash;
use Framework\Support\Observer;


class HashPasswordObserver extends Observer
{
    protected $targetClass = User::class;
    protected $hook = 'beforeUpdate'; 

    protected function observe(object $target): void
    {
        /** @var User $target */
        $password = $target->password;
        $target->set('password', Hash::make($password));
    }
}