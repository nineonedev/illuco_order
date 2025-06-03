<?php

namespace Framework\Database\Model\Entities\Casts;

use Framework\Database\Contracts\CastInterface;

class FloatCast implements CastInterface
{
    public function cast($value)
    {
        return (float) $value;
    }

    public function recast($value)
    {
        return (float) $value;
    }
}
