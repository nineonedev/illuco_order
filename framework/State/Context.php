<?php

namespace Framework\State;

class Context
{
    /**
     * 현재 컨텍스트 전용 상태 (예: 현재 요청, 현재 세션 등)
     *
     * @var array
     */
    protected $data = [];

    /**
     * 등록된 스토어들
     *
     * @var array<string, Store>
     */
    protected $stores = [];

    /**
     * 전역 데이터 (Registry처럼 쓰이지만 context 내부에서 관리)
     *
     * @var array
     */
    protected $globals = [];

    /**
     * Store를 등록합니다.
     */
    public function addStore(string $name, Store $store): void
    {
        $this->stores[$name] = $store;
    }

    /**
     * 특정 저장소에서 값을 가져옵니다.
     */
    public function getFrom(string $store, string $key, $default = null)
    {
        return isset($this->stores[$store])
            ? $this->stores[$store]->get($key, $default)
            : $default;
    }

    /**
     * 특정 저장소에 값을 저장합니다.
     */
    public function setTo(string $store, string $key, $value): void
    {
        if (isset($this->stores[$store])) {
            $this->stores[$store]->put($key, $value);
        }
    }

    /**
     * 특정 저장소에서 값을 제거합니다.
     */
    public function forgetFrom(string $store, string $key): void
    {
        if (isset($this->stores[$store])) {
            $this->stores[$store]->forget($key);
        }
    }

    /**
     * 현재 Context 데이터 저장
     */
    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->data;
    }

    /**
     * 전역 데이터 등록
     */
    public function setGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    public function getGlobal(string $key, $default = null)
    {
        return $this->globals[$key] ?? $default;
    }

    public function allGlobals(): array
    {
        return $this->globals;
    }
}
