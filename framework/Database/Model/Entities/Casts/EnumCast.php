<?php

namespace Framework\Database\Model\Entities\Casts;

use Framework\Database\Contracts\CastInterface;
use InvalidArgumentException;

class EnumCast implements CastInterface
{
    protected string $enumClass;

    public function __construct(string $enumClass)
    {
        if (!method_exists($enumClass, 'from') || !method_exists($enumClass, 'value')) {
            throw new InvalidArgumentException("Enum class [$enumClass] must implement from() and value() methods.");
        }

        $this->enumClass = $enumClass;
    }

    public function cast($value)
    {
        return $this->enumClass::from($value);
    }

    public function recast($value)
    {
        return $value instanceof $this->enumClass
            ? $value->value()
            : $value;
    }
}
