<?php

namespace Framework\Security\Encryption; 

interface EncrypterInterface
{
    public function encrypt(string $value): string;

    public function decrypt(string $payload): string;
}