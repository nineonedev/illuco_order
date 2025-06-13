<?php 

namespace App\Domains\User\Observers;

use App\Domains\User\Entities\User;
use App\Domains\User\Repositories\UserRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Repositories\Obserable;
use Framework\Http\Request;

class AfterCreateUserObserver implements Obserable
{
    public function observe(Entity $userable, Request $request): void
    {
        $userableClass = get_class($userable);

        $attributes = array_merge(
            $request->safe(),
            User::createMorphData($userableClass::alias(), $userable->id)
        );
        
        $userable = User::make($attributes);
        UserRepository::make()->save($userable);
    }
}