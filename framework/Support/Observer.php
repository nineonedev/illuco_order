<?php

namespace Framework\Support;

use Framework\Support\Contracts\ObserverInterface;

abstract class Observer implements ObserverInterface
{
    protected string $hook;
    protected string $targetClass;

    public function hook(): string
    {
        return $this->hook;
    }

    public function targetClass(): string
    {
        return $this->targetClass;
    }

    public function handle(object $target): void
    {
        if (!isset($this->hook, $this->targetClass)) {
            throw new \LogicException("Observer must define \$hook and \$targetClass.");
        }
        
        $expected = $this->targetClass();

        if (!($target instanceof $expected)) {
            return;
        }

        $this->observe($target);
    }

    abstract protected function observe(object $target): void;
}
