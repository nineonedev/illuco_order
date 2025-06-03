<?php

namespace Framework\Database\Entities\Casts;

use Framework\Database\Contracts\CastInterface;

class DateTimeCast implements CastInterface
{
    public function cast($value)
    {
        return $value ? new \DateTime($value) : null;
    }

    public function recast($value)
    {
        return $value instanceof \DateTime
            ? $value->format('Y-m-d H:i:s')
            : $value;
    }
}
