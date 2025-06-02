<?php

namespace Framework\Validation\Rules;

class Required extends Rule
{
    /**
     * Validate that the value is not empty.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return !empty($value);
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.required');
    }
}
