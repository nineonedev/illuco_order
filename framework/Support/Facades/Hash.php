<?php

namespace Framework\Support\Facades;

use Framework\Security\Hash\BcryptHasher;

/**
 * @method public string make(string $value, array $options = [])
 * @method public bool check(string $value, string $hashedValue, array $options = [])
 * @method public bool needsRehash(string $hashedValue, array $options = [])
 * @see BcryptHasher
 */
class Hash extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BcryptHasher::class;
    }
}
