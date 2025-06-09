<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\Entities\User;
use App\Domains\User\Observers\HashPasswordObserver;
use Framework\Constants\AuthConstants;
use Framework\Database\ORM\Repositories\Repository;
use Framework\Database\ORM\Repositories\RepositoryEvent;

class UserRepository extends Repository
{
    protected bool $preventsLazyLoading = true;

    public function table(): string
    {
        return 'users';
    }

    public function entityClass(): string
    {
        return User::class;
    }

    protected function registerObservers(): void
    {
        $this->on(RepositoryEvent::BEFORE_CREATE, HashPasswordObserver::class);
    }

    /**
     * 인증 후 유저 정보를 세션에 저장 (auth() 활용)
     */
    public function setUserToSession(User $user): void
    {
        // 로그인 세션 등록 (user_id 등은 내부적으로 처리)
        auth()->login($user);
    }

    /**
     * RememberToken 발급 및 DB, 쿠키 저장
     */
    public function createRememberToken(User $user): string
    {
        $token = bin2hex(random_bytes(32));
        $user->remember_token = $token;
        $this->save($user);
        cookie()->set(AuthConstants::REMEMBER_TOKEN_KEY, $token, 60 * 24 * 30);
        return $token;
    }

    /**
     * RememberToken 제거 (DB, 쿠키)
     */
    public function deleteRememberToken(User $user): void
    {
        $user->remember_token = null;
        $this->save($user);
        cookie()->forget(AuthConstants::REMEMBER_TOKEN_KEY);
    }

    /**
     * RememberToken으로 유저 조회
     */
    public function findByRememberToken(?string $token): ?User
    {
        if (!$token) return null;
        return $this->builder->where(AuthConstants::REMEMBER_TOKEN_KEY, $token)->first();
    }

    /**
     * RememberToken 자동로그인 시도. 성공시 true, 실패시 false 반환.
     */
    public function attemptRememberTokenLogin(): bool
    {
        $rememberToken = cookie()->get(AuthConstants::REMEMBER_TOKEN_KEY);
        $user = $this->findByRememberToken($rememberToken);

        if ($user) {
            // 인증 세션도 항상 auth()로 관리
            auth()->login($user);
            $this->createRememberToken($user); // 토큰 갱신
            return true;
        }

        return false;
    }

    /**
     * 로그인 시 remember me 체크박스에 따라 토큰 생성/삭제
     */
    public function processRememberMe($request, User $user): void
    {
        if ($request->body(AuthConstants::REMEMBER_ME_INPUT)) {
            $this->createRememberToken($user);
        } else {
            $this->deleteRememberToken($user);
        }
    }
}
