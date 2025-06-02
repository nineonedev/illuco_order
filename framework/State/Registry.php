<?php

namespace Framework\State;

class Registry
{
    /**
     * @var array
     */
    protected static $instances = [];

    /**
     * 레지스트리에 값을 등록합니다.
     */
    public static function set(string $key, $value): void
    {
        static::$instances[$key] = $value;
    }

    /**
     * 등록된 값을 가져옵니다.
     */
    public static function get(string $key, $default = null)
    {
        return static::$instances[$key] ?? $default;
    }

    /**
     * 등록된 값을 제거합니다.
     */
    public static function forget(string $key): void
    {
        unset(static::$instances[$key]);
    }

    /**
     * 모든 등록된 인스턴스를 반환합니다.
     */
    public static function all(): array
    {
        return static::$instances;
    }

    /**
     * 전체를 초기화합니다.
     */
    public static function flush(): void
    {
        static::$instances = [];
    }
}
