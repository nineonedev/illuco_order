<?php

namespace Framework\Validation\Rules;

class Nullable extends Rule
{
    public function passes($value): bool
    {
        return $value === null || $value === '';
    }

    public function message(): string
    {
        return '';
    }
}
