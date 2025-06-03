<?php

namespace Framework\Database\Entities\Casts;

use Framework\Database\Contracts\CastInterface;

class ArrayCast implements CastInterface
{
    public function cast($value)
    {
        return is_string($value) ? json_decode($value, true) : (array) $value;
    }

    public function recast($value)
    {
        return json_encode($value);
    }
}
