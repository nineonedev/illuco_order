<?php

namespace Framework\Validation\Rules;


class MinLength extends Rule
{
    protected $minLength;

    public function __construct($minLength)
    {
        $this->minLength = $minLength;
    }

    /**
     * Validate the value to be greater than or equal to the min length.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return strlen($value) >= $this->minLength;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.minLength', [$this->minLength]);
    }
}
