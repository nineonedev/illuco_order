<?php

namespace Framework\Database\ORM\Casts;

class BoolCast implements CastInterface
{
    public function set($value)
    {
        return $this->convertToBool($value) ? 1 : 0;
    }

    public function get($value)
    {
        return $this->convertToBool($value);
    }

    protected function convertToBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_null($value)) {
            return false;
        }

        if (is_int($value)) {
            return $value !== 0;
        }

        if (is_string($value)) {
            $value = strtolower(trim($value));
            return in_array($value, ['1', 'true', 'on'], true);
        }

        return (bool) $value;
    }
}
