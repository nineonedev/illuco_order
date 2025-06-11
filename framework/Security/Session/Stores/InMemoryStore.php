<?php

namespace Framework\Security\Session\Stores;

use Framework\Security\Session\SessionStore;

class InMemoryStore extends SessionStore
{
    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->id = session_id();
        $this->data = $_SESSION ?? [];
        $this->started = true;
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
        $this->id = session_id();
        // session_id가 바뀌었으니 데이터는 그대로 유지
    }

    public function invalidate(): void
    {
        $this->flush(); // $this->data = []; 처리
        $this->save();  // $_SESSION = [];
        session_destroy();
        $this->id = '';
        $this->started = false;
    }

    public function save(): void
    {
        $_SESSION = $this->data;
    }
}
