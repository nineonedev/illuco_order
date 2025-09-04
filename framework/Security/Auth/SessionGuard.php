<?php

namespace Framework\Security\Auth;

use App\Domains\User\Entities\User;
use Framework\Security\Auth\Contracts\GuardInterface;
use Framework\Security\Auth\Providers\AuthenticatableInterface;
use Framework\Security\Auth\Providers\SupportsTempPasswordInterface;
use Framework\Security\Auth\Providers\UserProviderInterface;
use Framework\Security\Session\Contracts\SessionInterface;
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


    // protected function validateCredentials(AuthenticatableInterface $user, array $credentials): bool
    // {
    //     return Hash::check($credentials['password'], $user->getAuthPassword()); 
    // }
    protected function validateCredentials(AuthenticatableInterface $user, array $credentials): bool
    {
        $plain = $credentials['password'] ?? null;

        if (!$plain) {
            return false;
        }

        // 1) 기본 비밀번호
        if (Hash::check($plain, $user->getAuthPassword())) {
            return true;
        }

        // 2) 임시 비밀번호
        if ($user instanceof SupportsTempPasswordInterface) {
            $hash = $user->getTempPasswordHash();
            $exp  = $user->getTempPasswordExpiresAt();

            if ($hash && $exp && $exp >= new \DateTimeImmutable() && Hash::check($plain, $hash)) {
                // 성공 즉시 만료
                $user->clearTempPassword();
                return true;
            }
        }

        return false;
    }


    public function login(AuthenticatableInterface $user): void
    {
        if (!$this->session->started()) {
            $this->session->start();
        }

        $this->session->set('user_id', $user->getAuthIdentifier()); 
        $this->session->regenerate();
        $this->user = $user;
    }

    public function setUser(AuthenticatableInterface $user): void
    {
        $this->session->set('user_id', $user->getAuthIdentifier()); 
        $this->user = $user; 
    }

    public function user(): ?User
    {
        if ($this->user) {
            return $this->user; 
        }

        $id = $this->session->get('user_id');

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

    public function id()
    {
        return $this->user() ? $this->user()->getAuthIdentifier() : null;
    }
}