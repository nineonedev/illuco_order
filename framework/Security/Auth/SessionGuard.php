<?php

namespace Framework\Security\Auth;

use Framework\Security\Contracts\AuthenticatableInterface;
use Framework\Security\Contracts\GuardInterface;
use Framework\Security\Contracts\SessionInterface;
use Framework\Security\Contracts\UserProviderInterface;

class SessionGuard implements GuardInterface
{
    protected string $name = 'auth_user_id'; 

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
        return password_verify($user->getAuthPassword(), $credentials['password']); 
    }

    public function login(AuthenticatableInterface $user): void
    {
        $this->session->set($this->name, $user->getAuthIdentifier()); 
        $this->user = $user; 
    }

    public function user(): ?AuthenticatableInterface
    {
        if ($this->user) {
            return $this->user; 
        }

        $id = $this->session->get($this->name); 

        if ($id) {
            return $this->user = $this->provider->retrieveById($id); 
        }

        return null; 
    }

    public function logout(): void
    {
        $this->session->forget($this->name); 
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