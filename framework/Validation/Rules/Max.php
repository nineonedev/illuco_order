<?php

namespace Framework\Validation\Rules;

class Max extends Rule
{
    protected $maxValue;

    public function __construct($maxValue)
    {
        $this->maxValue = $maxValue;
    }

    /**
     * Validate the value to be less than or equal to the max value.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return $value <= $this->maxValue;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.max', [$this->maxValue]);
    }
}
