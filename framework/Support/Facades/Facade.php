<?php

namespace Framework\Support\Facades;

abstract class Facade
{
    /**
     * The resolved object instances cache.
     *
     * @var array<string, object>
     */
    protected static array $resolvedInstances = [];

    /**
     * Get the registered name or class of the component.
     *
     * @return string
     */
    abstract protected static function getFacadeAccessor(): string;

    /**
     * Get the root object behind the facade.
     *
     * @return object
     */
    protected static function getFacadeRoot(): object
    {
        $name = static::getFacadeAccessor();

        if (isset(static::$resolvedInstances[$name])) {
            return static::$resolvedInstances[$name];
        }

        // app() 헬퍼로 인스턴스 생성 또는 가져오기
        return static::$resolvedInstances[$name] = app($name);
    }
    
    /**
     * @return object
     */
    public static function getInstance()
    {
        return static::getFacadeRoot();
    }

    /**
     * Swap the resolved instance (useful for testing or mocking).
     *
     * @param object $instance
     * @return void
     */
    public static function swap(object $instance): void
    {
        static::$resolvedInstances[static::accessor()] = $instance;
    }

    /**
     * Clear the resolved instance from cache.
     *
     * @return void
     */
    public static function clearResolvedInstance(): void
    {
        unset(static::$resolvedInstances[static::accessor()]);
    }

    /**
     * Clear all resolved instances cache.
     *
     * @return void
     */
    public static function clearAllResolvedInstances(): void
    {
        static::$resolvedInstances = [];
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $method, array $arguments)
    {
        $instance = static::getFacadeRoot();

        if (!method_exists($instance, $method)) {
            throw new \BadMethodCallException(
                "Method {$method} does not exist on class " . get_class($instance)
            );
        }

        return $instance->$method(...$arguments);
    }
}
