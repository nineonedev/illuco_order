<?php

namespace Framework\Validation\Rules;

class Number extends Rule
{
    /**
     * Validate that the value is a number.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return is_numeric($value);
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.number');
    }
}
