<?php

namespace Framework\Validation\Rules;

class Email extends Rule
{
    /**
     * Validate the value to be a valid email.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        // Check if the value is a valid email
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.email');
    }
}
