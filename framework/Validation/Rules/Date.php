<?php

namespace Framework\Validation\Rules;

class Date extends Rule
{
    /**
     * Validate the value to be a valid date.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        // Check if the value is a valid date
        return (bool) strtotime($value);
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.date');
    }
}
