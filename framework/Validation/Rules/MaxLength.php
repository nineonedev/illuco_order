<?php

namespace Framework\Validation\Rules;


class MaxLength extends Rule
{
    protected $maxLength;

    public function __construct($maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * Validate the value to be less than or equal to the max length.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return strlen($value) <= $this->maxLength;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.maxLength', [$this->maxLength]);
    }
}
