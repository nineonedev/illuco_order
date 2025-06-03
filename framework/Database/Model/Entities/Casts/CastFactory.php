<?php

namespace Framework\Database\Model\Entities\Casts;

use Framework\Database\Contracts\CastInterface;
use InvalidArgumentException;

class CastFactory
{
    protected static array $casts = [
        'bool'     => BoolCast::class,
        'int'      => IntegerCast::class,
        'float'    => FloatCast::class,
        'string'   => StringCast::class,
        'array'    => ArrayCast::class,
        'datetime' => DateTimeCast::class,
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
