<?php

namespace Framework\Security\Session;

use Framework\Constants\AuthConstants;
use Framework\Database\Schema\Blueprint;
use Framework\Security\Contracts\SessionInterface;
use Framework\Support\Facades\Schema;

class DatabaseStore implements SessionInterface
{
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
                $table->foreignId('user_id')->nullable()->constrained('users')->onDeleteCascade();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('last_activity')->nullable();
                $table->longText('payload');
                $table->timestamps();
            });
        }
    }


    protected function getCookieLifetimeMinutes(): int
    {
        $seconds = config('auth.session.lifetime', 60 * 30); 
        return ceil($seconds / 60);
    }

    /**
     * 세션 시작: 쿠키의 세션ID로 DB에서 데이터를 로드하거나, 없으면 새로 만듦
     */
    public function start(): void
    {
        $this->ensureTableExists();

        if ($this->started) return;

        // 기존 쿠키 확인(없을 때만 새로)
        $sessionId = cookie()->get(AuthConstants::SESSION_ID);

        if ($sessionId) {
            $this->id = $sessionId;
        } else {
            $this->id = bin2hex(random_bytes(20));

            // 쿠키 새로 발급 (첫 진입)
            cookie()->set(
                AuthConstants::SESSION_ID,
                $this->id,
                $this->getCookieLifetimeMinutes(),
                '/',
                '',
                null,
                true
            );
        }

        $record = db()->table('sessions')->where('id', $this->id)->first();

        if ($record) {
            if ($this->isTimeout($record->last_activity)) {
                $this->invalidateByTimeout();
            } else {
                $this->data = @unserialize($record->payload) ?: [];
            }
        } else {
            $this->data = [];
        }

        $this->started = true;
    }

    /**
     * 세션 만료(타임아웃) 여부
     */
    protected function isTimeout($lastActivity): bool
    {
        $lifetimeSeconds = config('auth.session.lifetime', 60 * 30); 
        if (!$lastActivity) return false;
        return strtotime($lastActivity) < (time() - $lifetimeSeconds);
    }

    /**
     * 세션 만료(타임아웃) 처리 - 기존 세션 삭제 + 신규 세션 생성
     */
    public function invalidateByTimeout(): void
    {
        db()->table('sessions')->where('id', $this->id)->delete();
        $this->id = bin2hex(random_bytes(20));
        $this->data = [];
        // 새로운 빈 세션 쿠키 발급
        cookie()->set(
            AuthConstants::SESSION_ID,
            $this->id,
            $this->getCookieLifetimeMinutes(),
            '/',
            '',
            null,
            true
        );
    }

    /**
     * 세션ID 재발급 (로그인/중요 이벤트시 보안강화용)
     */
    public function regenerate(): void
    {
        db()->table('sessions')->where('id', $this->id)->delete();
        $this->id = bin2hex(random_bytes(20));
        // 새 쿠키 부여 (이전 쿠키 삭제 + 새 쿠키 발급)
        cookie()->set(
            AuthConstants::SESSION_ID,
            $this->id,
            $this->getCookieLifetimeMinutes(),
            '/',
            '',
            null,
            true
        );
        $this->data = [];
    }

    /**
     * 세션 완전 무효화(로그아웃 등)
     */
    public function invalidate(): void
    {
        db()->table('sessions')->where('id', $this->id)->delete();
        cookie()->forget(AuthConstants::SESSION_ID); // 쿠키까지 삭제
        $this->id = bin2hex(random_bytes(20));
        $this->data = [];
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

    /**
     * DB에 세션 데이터 저장
     */
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
