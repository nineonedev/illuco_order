<?php

namespace Framework\Security\Hash;

use Framework\Security\Contracts\HasherInterface;

class BcryptHasher implements HasherInterface 
{
    protected int $defaultCost = 10; 

    public function make(string $value, array $options = []): string
    {
        $cost = $options['cost'] ?? $this->defaultCost; 

        return password_hash($value, PASSWORD_BCRYPT, [
            'cost' => $cost,
        ]); 
    }

    public function check(string $value, string $hashedValue, array $options = []): bool
    {
        return password_verify($value, $hashedValue); 
    }

    public function needsRehash(string $hashedValue, array $options = []): bool
    {
        $cost = $options['cost'] ?? $this->defaultCost; 

        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, [
            'cost' => $cost,
        ]); 
    }
}