<?php 

namespace Framework\Support; 

class Str {
    public static function startsWith(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) === 0;
    }

    public static function endsWith(string $haystack, string $needle): bool
    {
        return substr($haystack, -strlen($needle)) === $needle; 
    }

    public static function contains(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) !== false; 
    }

    public static function camel(string $value): string
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value)); 
        $value = str_replace(' ', '', $value); 
        return lcfirst($value); 
    }

    public static function classBasename(string $fqcn): string
    {
        $fqcn = trim($fqcn, '\\');
        if (self::contains($fqcn, '\\')) {
            return substr($fqcn, strrpos($fqcn, '\\') + 1);
        }
        return $fqcn;
    }

    public static function plural(string $value): string
    {
        return $value . (self::endsWith($value, 's') ? 'es' : 's'); // 간단한 처리
    }

    public static function snake(string $value): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $value));
    }

    public static function before(string $subject, string $search): string
    {
        $pos = strpos($subject, $search);
        return $pos === false ? $subject : substr($subject, 0, $pos);
    }

    public static function beforeLast(string $subject, string $search): string
    {
        $pos = strrpos($subject, $search);
        return $pos === false ? $subject : substr($subject, 0, $pos);
    }

    public static function studly(string $value): string
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value)); 
        return str_replace(' ', '', $value); 
    }
}
