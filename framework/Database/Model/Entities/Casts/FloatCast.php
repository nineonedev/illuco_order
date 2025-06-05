<?php

namespace Framework\Database\Model\Casts;

class FloatCast implements CastInterface
{
    /**
     * Convert value to float for DB storage
     *
     * @param mixed $value
     * @return float
     */
    public function set($value)
    {
        return (float) $value;
    }

    /**
     * Convert value from DB to PHP float
     *
     * @param mixed $value
     * @return float
     */
    public function get($value)
    {
        return (float) $value;
    }
}
