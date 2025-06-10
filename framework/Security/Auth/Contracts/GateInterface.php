<?php

namespace Framework\Security\Auth\Contracts; 

interface GateInterface
{
    public function define(string $ability, callable $callback): void; 

    public function allows(string $ability, array $arguments = []): bool;

    public function defines(string $ability, array $argumentss = []): bool;
}