<?php

namespace Framework\Support;

class Arr
{
    public static function get(array $array, string $key, $default = null)
    {
        if ($key === '') {
            return $array;
        }

        $keys = explode('.', $key);

        foreach ($keys as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    public static function set(array &$array, string $key, $value): void
    {
        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $segment = array_shift($keys);

            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                $array[$segment] = [];
            }

            $array = &$array[$segment];
        }

        $array[array_shift($keys)] = $value;
    }

    public static function has(array $array, string $key): bool
    {
        return self::get($array, $key) !== null;
    }

    public static function forget(array &$array, string $key): void
    {
        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $segment = array_shift($keys);

            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                return;
            }

            $array = &$array[$segment];
        }

        unset($array[array_shift($keys)]);
    }
}
