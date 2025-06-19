<?php

namespace Framework\Validation\Rules;

class Integer extends Rule
{
    public function passes($value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public function message(): string
    {
        return lang('rule.integer');
    }
}
