<?php

namespace Framework\Validation\Rules;

class Unique extends Rule
{
    protected $table;
    protected $column;
    protected $exceptId;

    public function __construct($table, $column = null, $exceptId = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->exceptId = $exceptId;
    }

    public function passes($value): bool
    {
        $column = $this->column ?: $this->field;
        $query = db($this->table)->where($column, '=', $value);

        if ($this->exceptId !== null) {
            $query->where('id', '!=', $this->exceptId);
        }

        return empty($query->get());
    }

    public function message(): string
    {
        return transfer('rule.unique', 'system.' . $this->field)
            ?? lang('rule.unique', [$this->field]) 
            ?? "{$this->field} 값이 이미 존재합니다.";
    }
}
