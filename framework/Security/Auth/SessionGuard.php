<?php

namespace Framework\Security\Auth;

use Framework\Constants\AuthConstants;
use Framework\Security\Auth\Providers\AuthenticatableInterface;
use Framework\Security\Auth\Providers\UserProviderInterface;
use Framework\Security\Contracts\GuardInterface;
use Framework\Security\Contracts\SessionInterface;
use Framework\Support\Facades\Hash;

class SessionGuard implements GuardInterface
{
    protected UserProviderInterface $provider; 
    protected SessionInterface $session; 

    protected ?AuthenticatableInterface $user = null; 

    public function __construct(UserProviderInterface $provider, SessionInterface $session)
    {
        $this->provider = $provider; 
        $this->session = $session; 
    }

    public function attempt(array $credentials): bool
    {
        $user = $this->provider->retrieveByCredentials($credentials); 

        if (!$user){ 
            return false; 
        }

        if (!$this->validateCredentials($user, $credentials)) {
            return false; 
        }

        $this->login($user);
        return true; 
    }

    protected function validateCredentials(AuthenticatableInterface $user, array $credentials): bool
    {
        return Hash::check($credentials['password'], $user->getAuthPassword()); 
    }

    public function login(AuthenticatableInterface $user): void
    {
        $this->session->setUserId($user->getAuthIdentifier()); 
        $this->user = $user; 
    }

    public function user(): ?AuthenticatableInterface
    {
        if ($this->user) {
            return $this->user; 
        }

        $id = $this->session->userId();

        if ($id) {
            return $this->user = $this->provider->retrieveById($id); 
        }

        return null; 
    }

    public function logout(): void
    {
        $this->session->invalidate();
        $this->user = null; 
    }

    public function check(): bool
    {
        return $this->user() !== null; 
    }

    public function guest(): bool
    {
        return !$this->check();
    }
}