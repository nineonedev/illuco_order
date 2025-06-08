<?php

namespace Framework\Validation\Rules;

class StringRule extends Rule
{
    /**
     * Validate that the value is a string.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        // null 허용 X, 문자열만 true
        return is_string($value);
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.string');
    }
}
