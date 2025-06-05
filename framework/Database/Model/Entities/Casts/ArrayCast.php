<?php

namespace Framework\Database\Model\Casts;

class ArrayCast implements CastInterface
{
    /**
     * Convert array to JSON string for DB storage
     *
     * @param mixed $value
     * @return string|null
     */
    public function set($value)
    {
        if (is_null($value)) {
            return null;
        }

        return json_encode($value);
    }

    /**
     * Convert JSON string from DB to PHP array
     *
     * @param mixed $value
     * @return array|null
     */
    public function get($value)
    {
        if (is_null($value)) {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        return json_decode($value, true);
    }
}
