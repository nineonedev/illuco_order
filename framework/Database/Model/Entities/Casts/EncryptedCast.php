<?php

namespace Framework\Database\Model\Entities\Casts;

use Framework\Database\Contracts\CastInterface;
use RuntimeException;

class EncryptedCast implements CastInterface
{
    protected string $cipher = 'AES-256-CBC';
    protected string $key;
    protected string $iv;

    public function __construct()
    {
        $this->key = config('app.encryption_key'); // 32자
        $this->iv  = substr(hash('sha256', 'encryption_iv'), 0, 16);
    }

    public function cast($value)
    {
        if (!$value) return null;

        $decrypted = openssl_decrypt($value, $this->cipher, $this->key, 0, $this->iv);

        if ($decrypted === false) {
            throw new RuntimeException('Decryption failed.');
        }

        return $decrypted;
    }

    public function recast($value)
    {
        $encrypted = openssl_encrypt($value, $this->cipher, $this->key, 0, $this->iv);

        if ($encrypted === false) {
            throw new RuntimeException('Encryption failed.');
        }

        return $encrypted;
    }
}
