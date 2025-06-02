<?php

namespace Framework\Security\Contracts; 

interface EncrypterInterface
{
    public function encrypt(string $value): string;

    public function decrypt(string $payload): string;
}