<?php

namespace Framework\Security\Auth\Contracts; 

interface GateInterface
{
    public function define(string $ability, callable $callback): void;
    public function allows(string $ability, array $arguments = []): bool;
    public function denies(string $ability, array $arguments = []): bool;
    public function authorize(string $ability, array $arguments = []): bool;
    public function authorizeOrFail(string $ability, array $arguments = []): void;
    public function policy(string $class, string $policyClass): void;
}
