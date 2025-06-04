<?php

namespace Framework\Database\Model\Entities\Casts;

use Framework\Database\Contracts\CastInterface;
use Framework\Support\ValueObjects\Money;

class MoneyCast implements CastInterface
{
    protected string $currency;

    public function __construct(string $currency = 'KRW')
    {
        $this->currency = strtoupper($currency);
    }

    public function cast($value)
    {
        return new Money((int) $value, $this->currency);
    }

    public function recast($value)
    {
        return $value instanceof Money ? $value->value() : (int) $value;
    }
}
