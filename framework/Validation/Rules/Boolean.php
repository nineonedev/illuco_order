<?php

namespace Framework\Validation\Rules;

class Boolean extends Rule
{
    public function passes($value): bool
    {
        return in_array($value, [true, false, 0, 1, '0', '1'], true);
    }

    public function message(): string
    {
        return lang('rule.boolean');
    }
}
