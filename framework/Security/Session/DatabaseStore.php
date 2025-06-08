<?php

namespace Framework\Security\Session;

use Framework\Database\Schema\Blueprint;
use Framework\Security\Contracts\SessionInterface;
use Framework\Support\Facades\Schema;

class DatabaseStore implements SessionInterface
{
    public const SESSION_ID = 'SESSION_ID';

    protected string $id;
    protected array $data = [];
    protected bool $started = false;

    protected function ensureTableExists(): void
    {
        static $ensured = false;
        if ($ensured) return;
        $ensured = true;

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function(Blueprint $table) {
                $table->string('id', 64)->primary();
                $table->foreignId('user_id')->nullable()->constrained('users');
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

        $this->id = cookie()->get(static::SESSION_ID) ?? bin2hex(random_bytes(20));

        $record = db()->table('sessions')->where('id', $this->id)->first();

        if ($record) {
            $this->data = @unserialize($record->payload) ?: [];
        } else {
            $this->data = [];
        }

        // 1일(24시간) 유효, HttpOnly, Secure X (로컬 개발 환경 기준)
        cookie()->set(
            static::SESSION_ID,
            $this->id,
            60 * 24,
            '/',
            '',
            null,
            true
        );

        $this->started = true;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
        $this->save();
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function forget(string $key): void
    {
        unset($this->data[$key]);
        $this->save();
    }

    public function flush(): void
    {
        $this->data = [];
        $this->save();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function regenerate(): void
    {
        // 기존 세션ID 데이터 삭제 (필요시)
        db()->table('sessions')->where('id', $this->id)->delete();

        // 새 세션ID 생성
        $this->id = bin2hex(random_bytes(20));
        $this->save();

        // 새 쿠키 발급 (기존값 동일하게)
        cookie()->set(
            static::SESSION_ID,
            $this->id,
            60 * 24,
            '/',
            '',
            null,
            true
        );
    }

    public function invalidate(): void
    {
        db()->table('sessions')->where('id', $this->id)->delete();
        $this->id = bin2hex(random_bytes(20));
        $this->data = [];
        // 새 쿠키 할당 (실제 세션은 빈 상태)
        cookie()->set(
            static::SESSION_ID,
            $this->id,
            60 * 24,
            '/',
            '',
            null,
            true
        );
    }

    protected function save(): void
    {
        $userId = $this->data['user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $lastActivity = date('Y-m-d H:i:s');

        db()->table('sessions')->updateOrInsert(
            ['id' => $this->id],
            [
                'user_id'      => $userId,
                'ip_address'   => $ip,
                'user_agent'   => $userAgent,
                'last_activity'=> $lastActivity,
                'payload'      => serialize($this->data),
                'updated_at'   => $lastActivity,
            ]
        );
    }
}
