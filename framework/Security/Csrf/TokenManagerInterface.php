<?php

namespace Framework\Security\Csrf;

interface TokenManagerInterface
{
    public function generate(): string;

    public function token(): ?string;

    public function verify(string $token): bool;

    public function field(): string;
}