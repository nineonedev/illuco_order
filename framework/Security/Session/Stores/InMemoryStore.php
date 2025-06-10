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
        $this->data = $_SESSION;
        $this->started = true;
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
        $this->id = session_id();
    }

    public function invalidate(): void
    {
        $this->flush();
        session_destroy();
        $this->id = '';
        $this->started = false;
    }

    protected function save(): void
    {
        $_SESSION = $this->data;
    }
}
