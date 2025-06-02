<?php

namespace Framework\Validation\Rules;

class Step extends Rule
{
    protected $step;

    public function __construct($step)
    {
        $this->step = $step;
    }

    /**
     * Validate that the value is divisible by the step value.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return $value % $this->step === 0;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return lang('rule.step', [$this->step]);
    }
}
