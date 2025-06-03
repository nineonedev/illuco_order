<?php

namespace Framework\Database\Model\Entities\Casts;

use Framework\Database\Contracts\CastInterface;

class BoolCast implements CastInterface
{
    public function cast($value)
    {
        return (bool) $value;
    }

    public function recast($value)
    {
        return (bool) $value;
    }
}
