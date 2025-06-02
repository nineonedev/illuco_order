<?php

namespace Framework\Security\Encryption;

use Framework\Security\Contracts\EncrypterInterface;

class StringCipher 
{
    protected EncrypterInterface $encrypter;

    public function __construct(EncrypterInterface $encrypter)
    {
        $this->encrypter = $encrypter; 
    }

    public function encode(string $plain): string
    {
        return $this->encrypter->encrypt($plain); 
    }

    public function decode(string $cipher): string
    {
        return $this->encrypter->decrypt($cipher);
    }
}