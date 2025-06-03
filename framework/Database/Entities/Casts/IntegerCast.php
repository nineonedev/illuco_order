<?php

namespace Framework\Database\Entities\Casts;

use Framework\Database\Contracts\CastInterface;

class IntegerCast implements CastInterface
{
    public function cast($value)
    {
        return (int) $value;
    }

    public function recast($value)
    {
        return (int) $value;
    }
}