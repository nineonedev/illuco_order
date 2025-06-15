<?php

namespace Framework\Database\Query\Clauses;

class JoinClause
{
    protected string $type;
    protected string $table;

    /**
     * join 조건들 (on, orOn, where, orWhere 등)
     * 예: [['type' => 'on', 'first' => 'a.id', 'operator' => '=', 'second' => 'b.user_id']]
     */
    protected array $clauses = [];

    public function __construct(string $type, string $table)
    {
        $this->type = $type;
        $this->table = $table;
    }

    // ===== JOIN 조건 =====

    public function on(string $first, string $operator, string $second): self
    {
        return $this->addClause('on', $first, $operator, $second);
    }

    public function orOn(string $first, string $operator, string $second): self
    {
        return $this->addClause('orOn', $first, $operator, $second);
    }

    public function where(string $column, string $operator, $value): self
    {
        return $this->addWhereClause('where', $column, $operator, $value);
    }

    public function orWhere(string $column, string $operator, $value): self
    {
        return $this->addWhereClause('orWhere', $column, $operator, $value);
    }

    // ===== 내부 처리 =====

    protected function addClause(string $type, string $first, string $operator, string $second): self
    {
        $this->clauses[] = [
            'type' => $type,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
        ];
        return $this;
    }

    protected function addWhereClause(string $type, string $column, string $operator, $value): self
    {
        $this->clauses[] = [
            'type' => $type,
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];
        return $this;
    }

    // ===== Getter =====

    public function getType(): string
    {
        return $this->type;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getClauses(): array
    {
        return $this->clauses;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'table' => $this->table,
            'clauses' => $this->clauses,
        ];
    }
}
