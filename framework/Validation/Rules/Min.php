<?php

namespace Framework\Validation\Rules;

class Min extends Rule
{
    protected $minValue;

    public function __construct($minValue)
    {
        $this->minValue = $minValue;
    }

    /**
     * Validate the value to be greater than or equal to the min value.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return $value >= $this->minValue;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.min', [$this->minValue]);
    }
}
