<?php 

namespace Framework\Security\Auth\Providers;

use App\Domains\User\Repositories\UserRepository;

class UserEntityProvider implements UserProviderInterface
{
    public function retrieveByCredentials(array $credentials): ?AuthenticatableInterface
    {
        // 유연한 인증 필드 지원: 이메일 or username
        if (isset($credentials['email'])) {
            $user = UserRepository::where('email', $credentials['email'])->first();
        } elseif (isset($credentials['username'])) {
            $user = UserRepository::where('username', $credentials['username'])->first();
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
        $user = UserRepository::find($identifier);
        
        if ($user instanceof AuthenticatableInterface) {
            return $user;
        }
        return null;
    }
}