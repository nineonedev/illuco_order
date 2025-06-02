<?php

namespace Framework\Validation\Rules;

abstract class Rule
{
    /**
     * Check if the rule passes the validation.
     *
     * @param mixed $value
     * @return bool
     */
    abstract public function passes($value): bool;

    /**
     * Get the error message when the rule fails.
     *
     * @return string
     */
    public function message(): string
    {
        return "Validation failed.";
    }
}
