<?php 

namespace Framework\Security\Auth\Providers;

use App\Domains\User\Entities\User;
use App\Domains\User\Repositories\UserRepository;

class UserEntityProvider implements UserProviderInterface
{
    public function retrieveByCredentials(array $credentials): ?AuthenticatableInterface
    {
        // 유연한 인증 필드 지원: 이메일 or username
        if (isset($credentials['email'])) {
            $user = UserRepository::make()
                ->query()
                ->with([User::morphType(), 'roles.permissions'])
                ->where('email', $credentials['email'])
                ->first();
                
        } elseif (isset($credentials['username'])) {
            $user = UserRepository::make()
                ->query()
                ->with([User::morphType(), 'roles.permissions'])
                ->where('username', $credentials['username'])
                ->first();
        } else {
            return null;
        }

        if ($user instanceof AuthenticatableInterface) {
            return $user;
        }

        return null;
    }

    public function retrieveById($identifier): ?AuthenticatableInterface
    {
        $user = UserRepository::make()
            ->query()
            ->with([User::morphType(), 'roles.permissions'])
            ->find($identifier);
        
        if ($user instanceof AuthenticatableInterface) {
            return $user;
        }
        return null;
    }
}