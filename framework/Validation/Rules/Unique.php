<?php

namespace Framework\Validation\Rules;

use Framework\Support\Facades\DB;

class Unique extends Rule
{
    protected $table;
    protected $column;

    public function __construct($table, $column)
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
        // Get the database connection and query the table for the unique value
        $query = DB::table($this->table);
        $result = $query->where($this->column, '=', $value)->get();

        // Return true if the result is empty, meaning the value is unique
        return empty($result);
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string
     */
    public function message(): string
    {
        // Returning the translated message for the unique rule
        return lang('rule.unique');
    }
}
