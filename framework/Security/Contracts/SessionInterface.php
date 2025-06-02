<?php

namespace Framework\Security\Contracts; 

interface SessionInterface
{
    public function start(): void;

    public function has(string $key): bool;

    public function get(string $key, $default = null);

    public function set(string $key, $value): void;

    public function forget(string $key): void;

    public function flush(): void;

    public function all(): array;

    public function id(): string;

    public function regenerate(): void; 

    public function invalidate(): void;
}