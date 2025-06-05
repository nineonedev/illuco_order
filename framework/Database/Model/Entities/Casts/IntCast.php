<?php

namespace Framework\Database\Model\Casts;

class IntCast implements CastInterface
{
    /**
     * Convert value to integer for DB storage
     *
     * @param mixed $value
     * @return int
     */
    public function set($value)
    {
        return (int) $value;
    }

    /**
     * Convert value from DB to PHP integer
     *
     * @param mixed $value
     * @return int
     */
    public function get($value)
    {
        return (int) $value;
    }
}
