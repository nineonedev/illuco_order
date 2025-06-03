<?php

namespace Framework\Database\Contracts;

interface EntityInterface
{
    public function getAttributes(): array;

    public function getOriginal(): array;

    public function isDirty(?string $key = null): bool;

    public function isClean(?string $key = null): bool;

    public function getChanges(): array;

    public function __get($key);

    public function __set($key, $value): void;

    public function __isset($key): bool;

    public function __unset($key): void;

}
