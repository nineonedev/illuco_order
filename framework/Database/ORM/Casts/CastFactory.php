<?php

namespace Framework\Database\ORM\Casts;

use InvalidArgumentException;

class CastFactory
{
    /**
     * @var array<string, class-string<CastInterface>>
     */
    protected static $map = [
        'int'      => IntCast::class,
        'bool'     => BoolCast::class,
        'string'   => StringCast::class,
        'float'    => FloatCast::class,
        'array'    => ArrayCast::class,
        'decimal'  => DecimalCast::class,
        'datetime' => DateTimeCast::class,
        '?int'     => NullableIntCast::class,
    ];

    /**
     * Resolve cast class from type
     *
     * @param string $type
     * @return CastInterface
     */
    public static function resolve(string $type): CastInterface
    {
        if (isset(self::$map[$type])) {
            $class = self::$map[$type];
        } elseif (class_exists($type)) {
            $class = $type;
        } else {
            throw new InvalidArgumentException("Cast type [$type] not recognized.");
        }

        $instance = new $class();

        if (!$instance instanceof CastInterface) {
            throw new InvalidArgumentException("Cast class [$class] must implement CastInterface.");
        }

        return $instance;
    }

    /**
     * 사용자 정의 캐스트 타입 등록 (선택적 기능)
     *
     * @param string $alias
     * @param class-string<CastInterface> $class
     * @return void
     */
    public static function extend(string $alias, string $class): void
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException("Class [$class] not found.");
        }

        self::$map[$alias] = $class;
    }
}
