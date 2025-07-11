<?php

namespace Framework\Support;

class Optional
{
    protected $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function __get($key)
    {
        if (is_object($this->value)) {
            return $this->value->{$key} ?? null;
        }

        return null;
    }

    public function __call($method, $arguments)
    {
        if (is_object($this->value)) {
            return $this->value->{$method}(...$arguments);
        }

        return null;
    }
}
