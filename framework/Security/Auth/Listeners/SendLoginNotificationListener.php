<?php

namespace Framework\Security\Auth\Listeners;

use Framework\Bus\Contracts\EventInterface;
use Framework\Bus\QueueableListener;
use Framework\Security\Auth\Events\LoginAttemped;

class SendLoginNotificationListener extends QueueableListener
{
    public function handle(EventInterface $event): void
    {
        if (!$event instanceof LoginAttemped) {
            return; 
        }

        echo "[비동기 알림] {$event->user->email} 로그인 알림 전송됨. \n";
    }

    public function queue(): string
    {
        return 'emails'; 
    }

    public function delay(): int
    {
        return 2; 
    }
}