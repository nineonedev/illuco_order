<?php 

namespace Framework\Security\Auth\Providers;

use Framework\Security\Contracts\AuthenticatableInterface;
use Framework\Security\Contracts\UserProviderInterface;

class InMemoryUserProvider implements UserProviderInterface
{
    /** @var AuthenticatableInterface[] */
    protected array $users = [];

    public function __construct(array $users = [])
    {
        foreach ($users as $user) {
            if ($user instanceof AuthenticatableInterface) {
                $this->users[$user->getAuthIdentifier()] = $user; 
            }
        }
    }

    public function retrieveByCredentials(array $credentials): ?AuthenticatableInterface
    {
        foreach ($this->users as $user) {
            if (
                isset($credentials['email']) 
                && method_exists($user, 'getEmail') 
                // && $user->getEmail() === $credentials['email']
            ) {
                return $user;
            }
        }

        return null;
    }

    public function retrieveById($identifier): ?AuthenticatableInterface
    {
        return $this->users[$identifier] ?? null;
    }
}