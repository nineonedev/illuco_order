<?php

namespace Framework\Security\Session\Contracts;


interface SupportsFlashInterface
{
    public function flash(string $key, $value): void;

    public function reflash(): void;

    public function keep(array $keys): void;

    public function saveFlash(): void;
}
