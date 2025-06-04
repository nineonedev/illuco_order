<?php

namespace Framework\Support;

use Framework\Support\Contracts\ObserverInterface;

class CallbackRegistry
{
    protected array $callbacks = [];

    /**
     * 콜백 등록
     */
    public function register(
        string $targetClass,
        string $hook,
        ObserverInterface $observer
    ): void {
        $this->callbacks[$targetClass][$hook][] = $observer;
    }

    /**
     * 단일 객체에 대해 훅 실행
     */
    public function dispatch(string $hook, object $target): void
    {
        $targetClass = get_class($target);

        foreach ($this->callbacks[$targetClass][$hook] ?? [] as $observer) {
            $observer->handle($target);
        }
    }

    /**
     * 다수 객체에 대해 훅 실행
     *
     * @param string $hook
     * @param object[] $targets
     */
    public function run(string $hook, array $targets): void
    {
        foreach ($targets as $target) {
            $this->dispatch($hook, $target);
        }
    }
}
