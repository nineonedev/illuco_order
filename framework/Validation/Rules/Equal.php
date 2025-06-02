<?php

namespace Framework\Validation\Rules;

class Equal extends Rule
{
    protected $compareValue;

    public function __construct($compareValue)
    {
        $this->compareValue = $compareValue;
    }

    /**
     * Validate that the value is equal to the provided value.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return $value == $this->compareValue;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.equal');
    }
}
