<?php 

namespace Framework\Security\Contracts; 

interface GuardInterface
{
    public function attempt(array $crendentials): bool;

    public function login(AuthenticatableInterface $user): void;

    public function user(): ?AuthenticatableInterface;

    public function logout(): void;

    public function check(): bool;

    public function guest(): bool;
}