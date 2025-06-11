<?php

namespace Framework\Support;

use DateTimeZone;

class Date
{
    protected static ?DateTimeZone $defaultTimezone = null;

    public static function timezone(): DateTimeZone
    {
        if (!self::$defaultTimezone) {
            $tz = config('app.timezone', 'Asia/Seoul');
            self::$defaultTimezone = new DateTimeZone($tz);
        }

        return self::$defaultTimezone;
    }

    public static function now(): DateTimeEx
    {
        return new DateTimeEx('now');
    }

    public static function today(): DateTimeEx
    {
        return new DateTimeEx('today');
    }

    public static function tomorrow(): DateTimeEx
    {
        return new DateTimeEx('tomorrow');
    }

    public static function yesterday(): DateTimeEx
    {
        return new DateTimeEx('yesterday');
    }
}