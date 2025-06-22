<?php

namespace Framework\Security\Auth\Access;

use Framework\Security\Auth\Contracts\GateInterface;
use Framework\Support\Exceptions\Http\UnauthorizedException;

class Gate
{
    public static function define(string $ability, callable $callback): void
    {
        static::manager()->define($ability, $callback);
    }

    public static function allows(string $ability, array $arguments = []): bool
    {
        return static::manager()->allows($ability, $arguments);
    }

    public static function denies(string $ability, array $arguments = []): bool
    {
        return static::manager()->denies($ability, $arguments);
    }

    public static function policy(string $class, string $policyClass): void
    {
        static::manager()->policy($class, $policyClass);
    }

    public static function authorize(string $ability, array $arguments = []): bool
    {
        if (!static::allows($ability, $arguments)) {
            return false;
        }

        return true;
    }

    public static function authorizeOrFail(string $ability, array $arguments = []): void
    {
        if (!static::allows($ability, $arguments)) {
            throw new UnauthorizedException();
        }
    }

    protected static function manager(): GateInterface
    {
        return app(GateInterface::class);
    }
}
