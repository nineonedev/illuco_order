<?php

namespace Framework\Security\Auth\Listeners;

use Framework\Bus\Contracts\EventInterface;
use Framework\Bus\Contracts\ListenerInterface;
use Framework\Security\Auth\Events\LoginAttemped;

class UpdateLastLoginTimestampLIstener implements ListenerInterface
{
    protected UserRepository $repo; 

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo; 
    }

    public function handle(EventInterface $event): void
    {
        if (!$event instanceof LoginAttemped) {
            return; 
        } 

        $user = $event->user; 
        $this->repo->updateLastLoginAt($user->getAuthIdentifier(), now());

        echo "[즉시 DB] 로그인 시작 갱신 완료.\n";
    }
}