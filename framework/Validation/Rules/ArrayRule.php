<?php

namespace Framework\Validation\Rules;

class ArrayRule extends Rule
{
    public function passes($value): bool
    {
        return is_array($value);
    }

    public function message(): string
    {
        return lang('rule.array');
    }
}
