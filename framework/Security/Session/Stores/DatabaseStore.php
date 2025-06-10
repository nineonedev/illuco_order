<?php

namespace Framework\Security\Session\Stores;

use Framework\Constants\AuthConstants;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;
use Framework\Security\Session\SessionStore;

class DatabaseStore extends SessionStore
{
    protected function ensureTableExists(): void
    {
        static $ensured = false;
        if ($ensured) return;
        $ensured = true;

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function(Blueprint $table) {
                $table->string('id', 64)->primary();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDeleteCascade();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('last_activity')->nullable();
                $table->longText('payload');
                $table->timestamps();
            });
        }
    }

    public function start(): void
    {
        $this->ensureTableExists();

        if ($this->started) return;

        $sessionId = cookie()->get(AuthConstants::SESSION_ID);
        $this->id = $sessionId ?: bin2hex(random_bytes(20));

        if (!$sessionId) {
            cookie()->set(AuthConstants::SESSION_ID, $this->id, $this->getCookieLifetimeMinutes(), '/', '', null, true);
        }

        $record = db()->table('sessions')->where('id', $this->id)->first();

        if ($record && !$this->isTimeout($record->last_activity)) {
            $this->data = @unserialize($record->payload) ?: [];
        }

        $this->started = true;
    }

    public function regenerate(): void
    {
        db()->table('sessions')->where('id', $this->id)->delete();

        $this->id = bin2hex(random_bytes(20));
        $this->data = [];

        cookie()->set(AuthConstants::SESSION_ID, $this->id, $this->getCookieLifetimeMinutes(), '/', '', null, true);
    }

    public function invalidate(): void
    {
        db()->table('sessions')->where('id', $this->id)->delete();
        cookie()->forget(AuthConstants::SESSION_ID);

        $this->id = bin2hex(random_bytes(20));
        $this->data = [];
    }

    public function invalidateByTimeout(): void
    {
        db()->table('sessions')->where('id', $this->id)->delete();
        $this->id = bin2hex(random_bytes(20));
        $this->data = [];

        cookie()->set(AuthConstants::SESSION_ID, $this->id, $this->getCookieLifetimeMinutes(), '/', '', null, true);
    }

    protected function isTimeout($lastActivity): bool
    {
        $lifetimeSeconds = config('auth.session.lifetime', 60 * 30);
        if (!$lastActivity) return false;
        return strtotime($lastActivity) < (time() - $lifetimeSeconds);
    }

    protected function getCookieLifetimeMinutes(): int
    {
        $seconds = config('auth.session.lifetime', 60 * 30);
        return ceil($seconds / 60);
    }

    protected function save(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $lastActivity = date('Y-m-d H:i:s');

        db()->table('sessions')->updateOrInsert(
            ['id' => $this->id],
            [
                'user_id'      => $this->get('user_id'),
                'ip_address'   => $ip,
                'user_agent'   => $userAgent,
                'last_activity'=> $lastActivity,
                'payload'      => serialize($this->data),
                'updated_at'   => $lastActivity,
            ]
        );
    }
}
