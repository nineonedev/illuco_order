<?php

namespace Framework\Validation\Rules;

class Phone extends Rule
{
    /**
     * Validate that the value is a valid phone number.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return preg_match('/^\+?[1-9]\d{1,14}$/', $value) === 1;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.phone');
    }
}
