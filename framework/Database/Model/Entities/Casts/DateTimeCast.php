<?php

namespace Framework\Database\Model\Casts;

use DateTime;

class DateTimeCast implements CastInterface
{
    /**
     * Convert to DB string format
     *
     * @param mixed $value
     * @return string|null
     */
    public function set($value)
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }

        return (new DateTime($value))->format('Y-m-d H:i:s');
    }

    /**
     * Convert from DB to 'Y-m-d H:i:s' string
     *
     * @param mixed $value
     * @return string|null
     */
    public function get($value)
    {
        if (is_null($value)) {
            return null;
        }

        $datetime = new DateTime($value);
        return $datetime->format('Y-m-d H:i:s');
    }
}
