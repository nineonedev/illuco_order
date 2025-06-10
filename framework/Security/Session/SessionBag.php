<?php

namespace Framework\Security\Session;

use Framework\Security\Session\Contracts\SessionInterface;

class SessionBag
{
    protected string $bagKey;
    protected SessionInterface $session;

    protected array $flashNow = [];   // 이번 요청 중 접근 가능
    protected array $flashNext = [];  // 다음 요청까지 유지할 플래시
    protected array $data = [];       // 임시 저장소 (DB 저장 X)

    public function __construct(string $bagKey, SessionInterface $session)
    {
        $this->bagKey = $bagKey;
        $this->session = $session;

        // flash 데이터 로드
        $this->flashNow = $session->get("_flash.{$this->bagKey}", []);
        $this->session->forget("_flash.{$this->bagKey}");
    }

    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->flashNow)) {
            return $this->flashNow[$key];
        }

        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->flashNow) || array_key_exists($key, $this->data);
    }

    public function forget(string $key): void
    {
        unset($this->data[$key], $this->flashNow[$key]);
    }

    public function all(): array
    {
        return array_merge($this->data, $this->flashNow);
    }

    public function flush(): void
    {
        $this->data = [];
        $this->flashNow = [];
    }

    // 🔥 Flash Support

    public function flash(string $key, $value): void
    {
        $this->set($key, $value);
        if (!in_array($key, $this->flashNext, true)) {
            $this->flashNext[] = $key;
        }
    }

    public function reflash(): void
    {
        $this->flashNext = array_merge($this->flashNext, array_keys($this->flashNow));
    }

    public function keep(array $keys): void
    {
        foreach ($keys as $key) {
            if (isset($this->flashNow[$key])) {
                $this->flashNext[] = $key;
            }
        }
    }

    public function saveFlash(): void
    {
        if (!empty($this->flashNext)) {
            $flashData = [];

            foreach ($this->flashNext as $key) {
                if (array_key_exists($key, $this->data)) {
                    $flashData[$key] = $this->data[$key];
                }
            }

            $this->session->set("_flash.{$this->bagKey}", $flashData);
        }
    }
}
