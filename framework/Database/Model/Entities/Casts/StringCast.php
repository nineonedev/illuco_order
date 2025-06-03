<?php

namespace Framework\Database\Model\Entities\Casts;

use Framework\Database\Contracts\CastInterface;

class StringCast implements CastInterface
{
    public function cast($value)
    {
        return (string) $value;
    }

    public function recast($value)
    {
        return (string) $value;
    }
}