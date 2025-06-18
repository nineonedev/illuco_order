<?php

namespace Framework\Database\ORM\Casts;

class NullableIntCast implements CastInterface
{
    public function set($value)
    {
        return $value === null ? null : (int) $value;
    }

    public function get($value)
    {
        return $value === null ? null : (int) $value;
    }
}
