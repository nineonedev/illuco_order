<?php

namespace Framework\Database\ORM\Casts;

class NullableStringCast implements CastInterface
{
    public function set($value)
    {
        if ($value === null || $value === 'null' || $value === '') {
            return null;
        }

        return (string) $value;
    }

    public function get($value)
    {
        if ($value === null || $value === 'null' || $value === '') {
            return null;
        }

        return (string) $value;
    }
}
