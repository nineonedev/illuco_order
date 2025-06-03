<?php 

namespace Framework\Support;

use Framework\Support\Contracts\ObserverInterface;

class CallbackRegistry
{
    protected array $callbacks = [];

    public function register(
        string $targetClass, 
        string $hook, 
        ObserverInterface $observer
    ): void
    {
        $this->callbacks[$targetClass][$hook][] = $observer;
    }

    public function dispatch(string $hook, object $target): void
    {
        $targetClass = get_class($target);

        foreach ($this->callbacks[$hook][$targetClass] ?? [] as $observer) {
            $observer->handle($target); 
        }
    }
}