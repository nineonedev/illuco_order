<?php

namespace Framework\Validation\Rules;

class In extends Rule
{
    protected array $allowed;

    public function __construct(array $allowed)
    {
        $this->allowed = $allowed;
    }

    public function passes($value): bool
    {
        if (is_null($value)) {
            return false;
        }
        return in_array($value, $this->allowed, true);
    }

    public function message(): string
    {
        $values = implode(', ', $this->allowed);
        return lang('rule.in', [$values]) ?? "{$this->field} 필드는 다음 값 중 하나여야 합니다: {$values}.";
    }
}
