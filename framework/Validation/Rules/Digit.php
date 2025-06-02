<?php

namespace Framework\Validation\Rules;

class Digit extends Rule
{
    /**
     * Validate the value to contain only digits.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        // Check if the value contains only digits
        return preg_match('/^\d+$/', $value) === 1;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.digit');
    }
}
