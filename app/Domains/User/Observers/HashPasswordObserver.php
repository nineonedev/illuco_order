<?php

namespace App\Domains\User\Observers;

use App\Domains\User\Entities\User;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Repositories\Obserable;
use Framework\Http\Request;
use Framework\Support\Facades\Hash;

class HashPasswordObserver implements Obserable
{
    public function observe(Entity $user, Request $request): void
    {
        /** @var User $target */
        $user->password = Hash::make($user->password);
    }
}