<?php

namespace App\User\Observers;

use App\User\Entities\User;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Repositories\Obserable;
use Framework\Support\Facades\Hash;

class HashPasswordObserver implements Obserable
{
    public function observe(Entity $entity): void
    {
        /** @var User $target */
        $entity->password = Hash::make($entity->password);
    }
}