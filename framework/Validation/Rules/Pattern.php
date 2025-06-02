<?php

namespace Framework\Validation\Rules;

class Pattern extends Rule
{
    protected $pattern;

    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Validate that the value matches the given pattern.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return preg_match($this->pattern, $value) === 1;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.pattern');
    }
}
