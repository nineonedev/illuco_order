<?php

namespace Framework\Database\ORM\Casts;

use DateTime;

class DateCast implements CastInterface
{
    /**
     * Convert to DB string format (Y-m-d)
     *
     * @param mixed $value
     * @return string|null
     */
    public function set($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        if ($value instanceof DateTime) {
            return $value->format('Y-m-d');
        }

        return (new DateTime($value))->format('Y-m-d');
    }

    /**
     * Convert from DB value to Y-m-d string
     *
     * @param mixed $value
     * @return string|null
     */
    public function get($value)
    {
        if (is_null($value)) {
            return null;
        }

        $date = new DateTime($value);
        return $date->format('Y-m-d');
    }
}
