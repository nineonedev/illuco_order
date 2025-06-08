<?php

namespace Framework\Support\Facades;

use Framework\Security\Hash\BcryptHasher;

/**
 * @method static string make(string $value, array $options = [])
 * @method static bool check(string $value, string $hashedValue, array $options = [])
 * @method static bool needsRehash(string $hashedValue, array $options = [])
 * @see BcryptHasher
 */
class Hash extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BcryptHasher::class;
    }
}
