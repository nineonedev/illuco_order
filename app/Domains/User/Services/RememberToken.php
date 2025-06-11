<?php

namespace App\Domains\User\Services;

use App\Domains\User\Entities\User;
use App\Domains\User\Repositories\UserRepository;

class RememberToken
{
    const COOKIE_NAME = 'remember_token';
    const INPUT_NAME = 'remember_me';

    protected int $ttl; 

    public function __construct()
    {
        $this->ttl = config('auth.remember.ttl', 60 * 60 * 24 * 30);
    }

    /**
     * @return static
     */
    public static function make()
    {
        return new static();
    }

    public function create(User $user): string
    {
        $token = bin2hex(random_bytes(32));
        $user->remember_token = $token;
        UserRepository::make()->save($user);

        cookie()->set(self::COOKIE_NAME, $token, time() + $this->ttl);

        return $token;
    }

    public function delete(User $user): void
    {
        $user->remember_token = null;
        UserRepository::make()->save($user);

        cookie()->forget(self::COOKIE_NAME);
    }

    public function findUser(): ?User
    {
        $token = cookie()->get(self::COOKIE_NAME);
        if (!$token) return null;

        return UserRepository::where('remember_token', $token)->first();
    }

    public function check(): bool
    {
        if (!session()->started()) {
            session()->start();
        }

        $user = $this->findUser();

        if ($user) {
            session()->deleteCurrentDeviceSession($user->id);
            auth()->setUser($user);
            return true;
        }

        return false;
    }

    public function handleRememberMe($request, User $user): void
    {
        if ($request->body(self::INPUT_NAME)) {
            $this->create($user);
        } else {
            $this->delete($user);
        }
    }
}