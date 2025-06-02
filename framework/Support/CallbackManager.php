<?php

namespace Framework\Support;

class CallbackManager
{
    protected bool $done = false;
    protected array $beforeCallbacks = [];
    protected array $duringCallbacks = [];
    protected array $afterCallbacks = [];

    public function isDone(): bool
    {
        return $this->done;
    }

    public function before(callable $callback): void
    {
        $this->beforeCallbacks[] = $callback;
    }

    public function during(callable $callback): void
    {
        $this->duringCallbacks[] = $callback;
    }

    public function after(callable $callback): void
    {
        $this->afterCallbacks[] = $callback;
    }

    public function run(): void
    {
        if ($this->done) {
            return;
        }

        foreach ($this->beforeCallbacks as $callback) {
            $callback();
        }

        foreach ($this->duringCallbacks as $callback) {
            $callback();
        }

        foreach ($this->afterCallbacks as $callback) {
            $callback();
        }

        $this->done = true;
    }
}
