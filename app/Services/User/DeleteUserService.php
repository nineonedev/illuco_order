<?php 

namespace App\Services\User;

use App\Supports\Services\Service;

class DeleteUserService extends Service
{
    protected function handle(array $payload)
    {
        userClass = get_class($user); 

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