<?php

namespace Framework\Database\Query;

use Closure;
use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Paginator\Paginator;
use Framework\Database\Query\Grammars\Grammar;

class Builder
{
    protected ConnectionInterface $connection;
    protected Grammar $grammar;
    protected string $table;

    protected array $columns = ['*'];
    protected array $wheres = [];
    protected array $orders = [];
    protected array $joins = [];
    protected array $groups = [];
    protected array $havings = [];
    protected array $unions = [];
    protected ?int $limit = null;
    protected ?int $offset = null;

    public function __construct(ConnectionInterface $connection, Grammar $grammar, string $table)
    {
        $this->connection = $connection;
        $this->grammar = $grammar;
        $this->table = $table;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    public function addSelect(string $column): self
    {
        $this->columns[] = $column;
        return $this; 
    }

    public function select(...$columns): self
    {
        if (count($columns) === 1 && is_array($columns[0])) {
            $columns = $columns[0];
        }

        $this->columns = $columns;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->orders[] = compact('column', 'direction');
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function get(): array
    {
        if (empty($this->orders)) {
            $this->orderBy('created_at', 'desc');
        }
        
        [$sql, $bindings] = $this->grammar->compileSelect($this);
        return $this->connection->select($sql, $bindings);
    }

    public function pluck(string $column): array
    {
        $this->columns = [$column];

        [$sql, $bindings] = $this->grammar->compileSelect($this);
        $results = $this->connection->select($sql, $bindings);

        return array_map(fn($row) => $row->{$column}, $results);
    }

    public function first(): ?object
    {
        $this->limit(1);
        $results = $this->get();
        
        return $results[0] ?? null;
    }

    public function updateOrInsert(array $where, array $values): bool
    {
        $query = clone $this;
        foreach ($where as $column => $value) {
            $query->where($column, $value);
        }
        $exists = $query->first();

        if ($exists) {
            // UPDATE
            $this->where(function($q) use ($where) {
                foreach ($where as $column => $value) {
                    $q->where($column, $value);
                }
            });
            $this->update($values);
        } else {
            // INSERT
            $this->insert(array_merge($where, $values));
        }
        return true;
    }

    /**
     * @return static ? $this ? self
     */
    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getWheres(): array
    {
        return $this->wheres;
    }

    public function getOrders(): array
    {
        return $this->orders;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function upsert(array $rows, array $uniqueBy, array $updateColumns): int
    {
        [$sql, $bindings] = $this->grammar->compileUpsert($this->table, $rows, $uniqueBy, $updateColumns);
        return $this->connection->insert($sql, $bindings);
    }

    public function insertOrIgnore(array $rows): int
    {
        [$sql, $bindings] = $this->grammar->compileInsertOrIgnore($this->table, $rows);
        return $this->connection->insert($sql, $bindings);
    }

    public function insert(array $data): ?int
    {
        [$sql, $bindings] = $this->grammar->compileInsert($this->table, $data);
        return $this->connection->insert($sql, $bindings);
    }

    public function update(array $data): int
    {
        [$sql, $bindings] = $this->grammar->compileUpdate($this, $data);
        return $this->connection->update($sql, $bindings);
    }

    public function delete(): int
    {
        [$sql, $bindings] = $this->grammar->compileDelete($this);
        return $this->connection->delete($sql, $bindings);
    }

    public function whereNull(string $column): self
    {
        $this->wheres[] = [
            'type' => 'null',
            'column' => $column,
            'boolean' => 'and',
        ];

        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->wheres[] = [
            'type' => 'notNull',
            'column' => $column,
            'boolean' => 'and',
        ];

        return $this;
    }

    public function when($value, Closure $callback, ?Closure $default = null): self
    {
        if ($value instanceof Closure) {
            $value = $value();
        }

        if ($value) {
            $callback($this, $value);
        } elseif ($default) {
            $default($this, $value);
        }

        return $this;
    }

    /**
     * @param string|\Closure $column
     */
    public function where($column, $operator = null, $value = null): self
    {
        // Closure 지원 (서브쿼리, 복합 where)
        if ($column instanceof \Closure) {
            $query = new self($this->connection, $this->grammar, $this->table);
            $column($query);
            $this->wheres[] = [
                'type' => 'nested',
                'query' => $query,
                'boolean' => 'and',
            ];
            return $this;
        }

        // 기존: where('age', 30) → operator 생략되면 '=' 처리
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => 'and',
        ];

        return $this;
    }

    public function exists(): bool
    {
        $clone = clone $this;
        $clone->columns = [1]; // 또는 ['1']
        $clone->limit = 1;
        $clone->orders = []; // order by 무시

        [$sql, $bindings] = $this->grammar->compileSelect($clone);
        $result = $this->connection->select($sql, $bindings);

        return !empty($result);
    }
    
    public function whereIn(string $column, array $values): self
    {
        $this->wheres[] = [
            'type' => 'in',
            'column' => $column,
            'values' => $values,
            'boolean' => 'and',
        ];
        return $this;
    }

    public function orWhere(string $column, $operator = null, $value = null): self
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => 'or',
        ];

        return $this;
    }

    public function paginate(int $perPage = 15, int $page = 1): Paginator
    {
        // 전체 개수 조회
        $countQuery = clone $this;
        $countQuery->columns = ['COUNT(*) as aggregate'];
        $countQuery->orders = [];
        $countQuery->limit = null;
        $countQuery->offset = null;

        [$countSql, $countBindings] = $this->grammar->compileSelect($countQuery);
        $totalRows = $this->connection->select($countSql, $countBindings);
        $total = (int) ($totalRows[0]->aggregate ?? 0);

        // 페이지 결과 조회
        $this->limit($perPage)->offset(($page - 1) * $perPage);
        $items = $this->get();

        return new Paginator($items, $total, $perPage, $page);
    }

    public function count(string $column = '*'): int
    {
        $clone = clone $this;
        $clone->columns = ["COUNT({$column}) as aggregate"];
        $clone->orders = [];
        $clone->limit = null;
        $clone->offset = null;

        [$sql, $bindings] = $this->grammar->compileSelect($clone);
        $result = $this->connection->select($sql, $bindings);
        return (int) ($result[0]->aggregate ?? 0);
    }

    public function sum(string $column): float
    {
        return $this->aggregate("SUM({$column})");
    }

    public function avg(string $column): float
    {
        return $this->aggregate("AVG({$column})");
    }

    public function min(string $column): float
    {
        return $this->aggregate("MIN({$column})");
    }

    public function max(string $column): float
    {
        return $this->aggregate("MAX({$column})");
    }

    protected function aggregate(string $expression): float
    {
        $clone = clone $this;
        $clone->columns = ["{$expression} as aggregate"];
        $clone->orders = [];
        $clone->limit = null;
        $clone->offset = null;

        [$sql, $bindings] = $this->grammar->compileSelect($clone);
        $result = $this->connection->select($sql, $bindings);
        return (float) ($result[0]->aggregate ?? 0);
    }

    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = ['type' => 'inner', 'table' => $table, 'first' => $first, 'operator' => $operator, 'second' => $second];
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = ['type' => 'left', 'table' => $table, 'first' => $first, 'operator' => $operator, 'second' => $second];
        return $this;
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = ['type' => 'right', 'table' => $table, 'first' => $first, 'operator' => $operator, 'second' => $second];
        return $this;
    }

    public function getJoins(): array
    {
        return $this->joins;
    }

    public function whereExists(Closure $callback): self
    {
        $query = new self($this->connection, $this->grammar, $this->table);
        $callback($query);

        $this->wheres[] = [
            'type' => 'exists',
            'query' => $query,
            'boolean' => 'and',
        ];

        return $this;
    }

    public function orWhereExists(Closure $callback): self
    {
        $query = new self($this->connection, $this->grammar, $this->table);
        $callback($query);

        $this->wheres[] = [
            'type' => 'exists',
            'query' => $query,
            'boolean' => 'or',
        ];

        return $this;
    }

    public function whereRaw(string $raw): self
    {
        $this->wheres[] = [
            'type' => 'raw',
            'sql' => $raw,
            'boolean' => 'and',
        ];
        return $this;
    }

    public function groupBy(string ...$columns): self
    {
        foreach ($columns as $column) {
            $this->groups[] = $column;
        }
        return $this;
    }

    public function having(string $column, string $operator, $value): self
    {
        $this->havings[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => 'and',
        ];
        return $this;
    }

    public function orHaving(string $column, string $operator, $value): self
    {
        $this->havings[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => 'or',
        ];
        return $this;
    }

    public function union(Builder $query, bool $all = false): self
    {
        $this->unions[] = [
            'query' => $query,
            'all' => $all,
        ];

        return $this;
    }

    public function getUnions(): array
    {
        return $this->unions;
    }


    public function havingRaw(string $raw): self
    {
        $this->havings[] = [
            'type' => 'raw',
            'sql' => $raw,
            'boolean' => 'and',
        ];
        return $this;
    }

    // Getter
    public function getGroups(): array
    {
        return $this->groups;
    }

    public function getHavings(): array
    {
        return $this->havings;
    }


}
