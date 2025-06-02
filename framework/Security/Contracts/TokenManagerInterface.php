<?php

namespace Framework\Security\Contracts; 

interface TokenManagerInterface
{
    public function generate(): string;

    public function token(): ?string;

    public function verify(string $token): bool;
}