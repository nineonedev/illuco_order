<?php

namespace Framework\Security\Encryption;

use RuntimeException;

class Encrypter implements EncrypterInterface
{
    protected string $key; 
    protected string $cipher = 'AES-256-CBC';

    public function __construct(string $key)
    {
        if (strlen($key) < 32) {
            throw new RuntimeException("Encryption key must be at least 32 characters."); 
        }

        $this->key = substr(hash('sha256', $key, true), 0, 32); 
    }


    public function encrypt(string $value): string
    {
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher)); 
        $encrypted = openssl_encrypt($value, $this->cipher, $this->key, 0, $iv); 
        
        if ($encrypted === false) {
            throw new RuntimeException("Unable to encrypt the data."); 
        }

        $payload = base64_encode(json_encode([
            'iv' => base64_encode($iv),
            'value' => $encrypted,
        ]));

        return $payload;
    }

    public function decrypt(string $payload): string
    {
        $decoded = json_decode(base64_decode($payload), true); 

        if (!is_array($decoded) || !isset($decoded['iv'], $decoded['value'])) {
            throw new RuntimeException("Invalid payload for decryption."); 
        }

        $iv = base64_decode($decoded['iv']); 
        $value = base64_decode($decoded['value']); 

        $decrypted = openssl_decrypt($value, $this->cipher, $this->key, 0, $iv); 

        if ($decrypted === false) {
            throw new RuntimeException("Unable to decrypt the data."); 
        }

        return $decrypted;
    }
}