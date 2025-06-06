<?php

namespace Framework\Database\ORM\Repositories;

class CallbackManager
{
    /** @var array<string, callable[]> */
    protected array $callbacks = [];

    /**
     * 이벤트명에 콜백 추가
     * @param string $event
     * @param callable $callback
     * @return void
     */
    public function register(string $event, callable $callback): void
    {
        $this->callbacks[$event][] = $callback;
    }

    /**
     * 콜백 전체/특정 이벤트 해제
     * @param string|null $event
     * @return void
     */
    public function clear(?string $event = null): void
    {
        if ($event === null) {
            $this->callbacks = [];
        } else {
            unset($this->callbacks[$event]);
        }
    }

    /**
     * 이벤트 발생 시 모든 콜백 실행
     * @param string $event
     * @param mixed ...$args
     * @return void
     */
    public function fire(string $event, ...$args): void
    {
        if (empty($this->callbacks[$event])) return;
        foreach ($this->callbacks[$event] as $callback) {
            $callback(...$args);
        }
    }
}
