<?php

namespace Framework\Security\Session\Stores;

use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;
use Framework\Security\Session\SessionStore;

class DatabaseStore extends SessionStore
{
    protected bool $regenerated = false;

    public function start(): void
    {
        if ($this->started) return;

        $this->initializeSessionId();
        $this->loadSessionData();

        parent::start();
    }

    protected function initializeSessionId(): void
    {
        if ($this->id) return;

        $id = cookie()->get(self::SESSION_ID);

        if ($id) {
            $this->id = $id;
        } else {
            $this->regenerate();
        }
    }

    protected function loadSessionData(): void
    {
        $record = db()->table('sessions')->where('session_id', $this->id)->first();

        if ($record && !$this->isTimeout($record->last_activity)) {
            $this->data = @unserialize($record->payload) ?: [];
        } else {
            $this->data = [];
        }
    }

    public function regenerate(): void
    {
        if ($this->regenerated) return;

        if ($this->id) {
            db()->table('sessions')->where('session_id', $this->id)->delete();
        }

        $this->id = bin2hex(random_bytes(20));
        $this->updateCookie();
        $this->regenerated = true;
    }

    public function invalidate(): void
    {
        $this->invalidateSession(true);
        $this->started = false;
    }

    public function gc(): void
    {
        $expiredAt = now()->subSeconds(config('auth.session.lifetime', 1800));

        db()->table('sessions')
            ->where('last_activity', '<', $expiredAt->format('Y-m-d H:i:s'))
            ->where(function ($query) {
                $query->where('user_id', '!=', auth()->id())
                    ->orWhere('user_agent', '!=', request()->http()->userAgent());
            })
            ->delete();
    }

    public function deleteCurrentDeviceSession(?int $userId = null): void
    {
        if (!$userId) return;

        db()->table('sessions')
            ->where('user_id', $userId)
            ->where('user_agent', request()->http()->userAgent())
            ->orderByDesc('created_at')
            ->limit(1)
            ->delete();
    }

    protected function invalidateSession(bool $resetCookie): void
    {
        $this->deleteSessionRecord();
        $this->resetSessionState();
        $this->handleCookie($resetCookie);
    }

    protected function deleteSessionRecord(): void
    {
        db()->table('sessions')->where('session_id', $this->id)->delete();
    }

    protected function resetSessionState(): void
    {
        $this->id = bin2hex(random_bytes(20));
        $this->data = [];
    }

    protected function handleCookie(bool $forget): void
    {
        if ($forget) {
            $this->forgetCookie();
        } else {
            $this->updateCookie();
        }
    }

    protected function forgetCookie(): void
    {
        cookie()->forget(self::SESSION_ID);
    }

    protected function updateCookie(): void
    {
        cookie()->set(
            self::SESSION_ID,
            $this->id,
            $this->getCookieLifetimeMinutes(),
            '/',
            '',
            null,
            true
        );
    }

    protected function isTimeout($lastActivity): bool
    {
        if (!$lastActivity) return false;

        $lifetime = config('auth.session.lifetime', 1800);
        return (time() - strtotime($lastActivity)) > $lifetime;
    }

    protected function getCookieLifetimeMinutes(): int
    {
        $seconds = config('auth.session.lifetime', 1800);
        return (int) ceil($seconds / 60);
    }

    public function save(): void
    {
        db()->table('sessions')->updateOrInsert(
            ['session_id' => $this->id],
            $this->buildSessionPayload()
        );
    }

    protected function buildSessionPayload(): array
    {
        $http = request()->http();
        $now = now()->format('Y-m-d H:i:s');

        return [
            'user_id'       => $this->get('user_id'),
            'ip_address'    => $http->ip(),
            'user_agent'    => $http->userAgent(),
            'last_activity' => $now,
            'payload'       => serialize($this->data),
            'updated_at'    => $now,
        ];
    }
}
