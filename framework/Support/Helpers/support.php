<?php

use Framework\Support\Date;
use Framework\Support\DateTimeEx;

if (!function_exists('now')) {
    function now(): DateTimeEx
    {
        return Date::now();
    }
}