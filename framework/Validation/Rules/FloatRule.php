<?php

namespace Framework\Validation\Rules;

class FloatRule extends Rule
{
    public function passes($value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    public function message(): string
    {
        return lang('validation.float');
    }
}
