<?php

use Framework\Support\Date;
use Framework\Support\DateTimeEx;
use Framework\Support\Logger;
use Framework\Support\Str;

if (!function_exists('now')) {
    function now(): DateTimeEx
    {
        return Date::now();
    }
}

if (!function_exists('class_basename')) {
    function class_basename(string $class_string): string
    {
        return Str::classBasename($class_string);
    }
}

if (!function_exists('logger')) {
    function logger(): Logger
    {
        return app(Logger::class);
    }
}


if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return Str::startsWith($haystack, $needle);
    }
}


if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return Str::contains($haystack, $needle);
    }
}

if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        return Str::endsWith($haystack, $needle);
    }
}
