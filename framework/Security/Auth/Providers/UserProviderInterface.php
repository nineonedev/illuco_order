<?php 

namespace Framework\Security\Auth\Providers;

interface UserProviderInterface
{
    public function retrieveByCredentials(array $credentials): ?AuthenticatableInterface;

    /**
     * @param mixed $identifier
     */
    public function retrieveById($identifier): ?AuthenticatableInterface;
}