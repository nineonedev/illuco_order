<?php

namespace Framework\Validation\Rules;

class DateISO extends Rule
{
    /**
     * Validate the value to be a valid ISO 8601 date format.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        // Check if the value matches the ISO 8601 date format (YYYY-MM-DD)
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.dateISO');
    }
}
