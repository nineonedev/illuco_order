<?php

namespace Framework\Database\ORM\Casts;

class StringCast implements CastInterface
{
    /**
     * Convert value to string for DB storage
     *
     * @param mixed $value
     * @return string
     */
    public function set($value)
    {
        return (string) $value;
    }

    /**
     * Convert value from DB to PHP string
     *
     * @param mixed $value
     * @return string|null
     */
    public function get($value)
    {
        if ($value === null) {
            return null;
        }

        return (string) $value;
    }
}
