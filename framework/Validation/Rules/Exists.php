<?php

namespace Framework\Validation\Rules;

class Exists extends Rule
{
    protected string $table;
    protected string $column;

    public function __construct(string $table, ?string $column = null)
    {
        $this->table = $table;
        $this->column = $column ?: 'id';
    }

    public function passes($value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        $query = db($this->table);
        $count = $query->where($this->column, '=', $value)->count();

        return $count > 0;
    }

    public function message(): string
    {
        return lang('validation.exists') ?: "{$this->field} 값이 존재하지 않습니다.";
    }
}
