<?php 

namespace App\Domains\User\Observers;

use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\RelationMap;
use Framework\Database\ORM\Repositories\Obserable;
use Framework\Http\Request;

class BeforeDeleteUserableObserver implements Obserable
{
    public function observe(Entity $user, Request $request): void
    {
        $userClass = get_class($user); 

        $type = $user->{$userClass::getMorphType()};
        $id = $user->{$userClass::getMorphId()}; 

        if (!$type || !$id) return; 

        $userableClass = RelationMap::resolveMorph($type);

        if (!class_exists($userableClass)) {
            return; 
        }

        $userable = $userableClass::find($id); 
        if ($userable) {
            $userable->repositoryClass()::make()->delete($userable); 
        }
    }
}