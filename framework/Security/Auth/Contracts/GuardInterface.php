<?php 

namespace Framework\Security\Auth\Contracts;

use Framework\Security\Auth\Providers\AuthenticatableInterface;

interface GuardInterface
{
    public function attempt(array $crendentials): bool;

    public function login(AuthenticatableInterface $user): void;

    public function user(): ?AuthenticatableInterface;

    public function logout(): void;

    public function check(): bool;

    public function guest(): bool;
}