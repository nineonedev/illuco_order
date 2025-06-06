<?php

namespace Framework\Database\ORM\Casts;

class BoolCast implements CastInterface
{
    /**
     * Convert value to boolean for DB storage
     *
     * @param mixed $value
     * @return int
     */
    public function set($value)
    {
        return $value ? 1 : 0;
    }

    /**
     * Convert value from DB to PHP boolean
     *
     * @param mixed $value
     * @return bool
     */
    public function get($value)
    {
        return (bool) $value;
    }
}
