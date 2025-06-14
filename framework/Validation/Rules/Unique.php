<?php

namespace Framework\Validation\Rules;


class Unique extends Rule
{
    protected $table;
    protected $column;

    public function __construct($table, $column = null)
    {
        $this->table = $table;
        $this->column = $column;
    }

    /**
     * Validate that the value is unique in the database.
     *
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        $column = $this->column ?: $this->field; 
        $query = db($this->table);
        $result = $query->where($column, '=', $value)->get();

        return empty($result);
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        return transfer('rule.unique', 'system.'.$this->field);
    }
}
