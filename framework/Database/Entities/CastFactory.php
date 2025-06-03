<?php

namespace Framework\Database\Factories;

use Framework\Database\Contracts\CastInterface;
use Framework\Database\Entities\Casts;
use InvalidArgumentException;

class CastFactory
{
    protected static array $casts = [
        'bool'     => Casts\BoolCast::class,
        'int'      => Casts\IntegerCast::class,
        'float'    => Casts\FloatCast::class,
        'string'   => Casts\StringCast::class,
        'array'    => Casts\ArrayCast::class,
        'datetime' => Casts\DateTimeCast::class,
    ];

    public static function resolve(string $type): CastInterface
    {
        if (!isset(self::$casts[$type])) {
            throw new InvalidArgumentException("Unsupported cast type [$type]");
        }

        $class = self::$casts[$type];

        return new $class;
    }
}
