<?php

namespace Framework\Database\ORM\Casts;

class NullableIntCast implements CastInterface
{
    public function set($value)
    {
        if ($value === null || $value === 'null') {
            return null;
        }

        return (int) $value;
    }

    public function get($value)
    {
        if ($value === null || $value === 'null') {
            return null;
        }

        return (int) $value;
    }
}
