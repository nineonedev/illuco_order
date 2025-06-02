<?php

namespace Framework\Validation\Rules;

class PhoneUS extends Rule
{
    /**
     * Validate that the value is a valid US phone number.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return preg_match('/^\(\d{3}\) \d{3}-\d{4}$/', $value) === 1;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.phoneUS');
    }
}
