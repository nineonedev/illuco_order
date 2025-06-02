<?php 

namespace Framework\Security\Contracts; 

interface UserProviderInterface
{
    public function retrieveByCredentials(array $credentials): ?AuthenticatableInterface;

    /**
     * @param mixed $identifier
     */
    public function retrieveById($identifier): ?AuthenticatableInterface;
}