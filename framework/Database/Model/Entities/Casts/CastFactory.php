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
        'enum'      => EnumCast::class,
        'encrypted' => EncryptedCast::class,
        'json' => JsonCast::class,
        'collection' => CollectionCast::class,
        'money'  => MoneyCast::class,
        'won'    => WonCast::class,
        'dollar' => DollarCast::class,
    ];

    public static function resolve($type): CastInterface
    {
        // 배열 형식: ['enum', UserRole::class]
        if (is_array($type)) {
            [$key, ...$args] = $type;
        }
        // 문자열 형식: 'enum:UserRole'
        elseif (is_string($type) && strpos($type, ':') !== false) {
            [$key, $argString] = explode(':', $type, 2);
            $args = explode(',', $argString);
        } else {
            $key = $type;
            $args = [];
        }

        if (!isset(self::$casts[$key])) {
            throw new InvalidArgumentException("Unsupported cast type [$key]");
        }

        $class = self::$casts[$key];

        return new $class(...$args);
    }
}
