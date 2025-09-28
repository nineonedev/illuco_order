<?php

namespace Framework\Database\Query;

use Closure;
use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Rel;
use Framework\Database\Paginator\Paginator;
use Framework\Database\Query\Clauses\JoinClause;
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

    public function newQuery(): self
    {
        return new self(
            $this->connection,
            $this->grammar,
            $this->table
        );
    }

    public function whereOrFail(): array
    {
        $records = $this->get();

        if (empty($records)) {
            throw new \RuntimeException("정보를 찾을 수 없습니다.");
        }

        return $records;
    }

    public function findManyOrFail(array $ids, string $primaryKey = 'id'): array
    {
        $records = $this->findMany($ids, $primaryKey);

        if (empty($records)) {
            throw new \RuntimeException("정보를 찾을 수 없습니다.");
        }

        return $records;
    }

    public function firstOrFail(): object
    {
        $record = $this->first();

        if (!$record) {
            throw new \RuntimeException("정보를 찾을 수 없습니다.");
        }

        return $record;
    }

    public function findOrFail($id, string $primaryKey = 'id'): object
    {
        $record = $this->where($primaryKey, '=', $id)->first();

        if (!$record) {
            throw new \RuntimeException("정보를 찾을 수 없습니다.");
        }

        return $record;
    }

    public function findMany(array $ids, string $primaryKey = 'id'): array
    {
        if (empty($ids)) {
            return [];
        }

        $this->whereIn($primaryKey, $ids);
        return $this->get(); // 기존 `get()` 메서드로 결과 조회
    }

    public function whereNotIn(string $column, array $values): self
    {
        // 비어있으면 조건을 추가하지 않음 (Laravel과 동일한 UX)
        if (empty($values)) {
            return $this;
        }

        $this->wheres[] = [
            'type' => 'notIn',
            'column' => $column,
            'values' => $values,
            'boolean' => 'and',
        ];

        return $this;
    }


    public function bulkDelete(array $ids, string $primaryKey = 'id'): int
    {
        if (empty($ids)) {
            return 0;
        }

        $this->whereIn($primaryKey, $ids);
        return $this->delete();
    }

    public function selectRaw(string $expression): self
    {
        $this->columns = [$expression];
        return $this;
    }

    public function groupRaw(string $raw): self
    {
        $this->groups[] = [
            'type' => 'raw',
            'sql' => $raw,
        ];

        return $this;
    }

    public function getBindings(): array
    {
        $bindings = [];

        // where
        foreach ($this->wheres as $where) {
            if ($where['type'] === 'basic') {
                $bindings[] = $where['value'];
            } elseif ($where['type'] === 'in') {
                foreach ($where['values'] as $val) {
                    $bindings[] = $val;
                }
            } elseif ($where['type'] === 'notIn') { // ← 추가
                foreach ($where['values'] as $val) {
                    $bindings[] = $val;
                }
            } elseif ($where['type'] === 'nested' || $where['type'] === 'exists') {
                $bindings = array_merge($bindings, $where['query']->getBindings());
            }
            // raw, null, notNull은 바인딩 없음
        }

        // having
        foreach ($this->havings as $having) {
            if ($having['type'] === 'basic') {
                $bindings[] = $having['value'];
            }
            // raw은 생략
        }

        // union
        foreach ($this->unions as $union) {
            $bindings = array_merge($bindings, $union['query']->getBindings());
        }

        return $bindings;
    }
    
    public function existsOrFail(): bool
    {
        if (!$this->exists()) {
            throw new \RuntimeException("정보를 찾을 수 없습니다.");
        }
        return true;
    }

    public function increment(string $column, int $amount = 1): int
    {
        return $this->update([
            $column => $this->grammar->raw("{$column} + {$amount}")
        ]);
    }

    public function decrement(string $column, int $amount = 1): int
    {
        return $this->update([
            $column => $this->grammar->raw("{$column} - {$amount}")
        ]);
    }

    public function clone(): self
    {
        return clone $this;
    }

    public function truncate(): void
    {
        $sql = "TRUNCATE TABLE " . $this->grammar->wrapTable($this->table);
        $this->connection->statement($sql);
    }

    public function existsByOrFail(array $where): bool
    {
        foreach ($where as $column => $value) {
            $this->where($column, $value);
        }

        if (!$this->exists()) {
            throw new \RuntimeException("정보를 찾을 수 없습니다.");
        }

        return true;
    }

    public function whereDate(string $column, string $operator, string $value): self
    {
        $this->wheres[] = [
            'type' => 'raw',
            'sql' => "DATE(" . $this->grammar->wrap($column) . ") {$operator} ?",
            'boolean' => 'and',
            'value' => $value,
        ];

        return $this;
    }

    public function whereBetween(string $column, array $values): self
    {
        if (count($values) !== 2) {
            throw new \InvalidArgumentException("whereBetween requires exactly 2 values.");
        }

        $this->wheres[] = [
            'type' => 'raw',
            'sql' => $this->grammar->wrap($column) . " BETWEEN ? AND ?",
            'boolean' => 'and',
            'values' => $values,
        ];

        return $this;
    }

    public function whereYear(string $column, string $operator, string $value): self
    {
        $this->wheres[] = [
            'type' => 'raw',
            'sql' => "YEAR(" . $this->grammar->wrap($column) . ") {$operator} ?",
            'boolean' => 'and',
            'value' => $value,
        ];

        return $this;
    }

    public function whereMonth(string $column, string $operator, string $value): self
    {
        $this->wheres[] = [
            'type' => 'raw',
            'sql' => "MONTH(" . $this->grammar->wrap($column) . ") {$operator} ?",
            'boolean' => 'and',
            'value' => $value,
        ];

        return $this;
    }

    protected function addJoinSub(string $type, SubQuery $subQuery, string $alias, Closure $callback): self
    {
        $sql = "({$subQuery->query->getGrammar()->compileSelect($subQuery->query)[0]}) as `{$alias}`";
        $join = new JoinClause($type, $sql);
        $callback($join);
        $this->joins[] = $join->toArray();
        return $this;
    }

    public function joinSub(SubQuery $subQuery, string $alias, Closure $callback): self
    {
        return $this->addJoinSub('inner', $subQuery, $alias, $callback);
    }

    public function leftJoinSub(SubQuery $subQuery, string $alias, Closure $callback): self
    {
        return $this->addJoinSub('left', $subQuery, $alias, $callback);
    }

    public function rightJoinSub(SubQuery $subQuery, string $alias, Closure $callback): self
    {
        return $this->addJoinSub('right', $subQuery, $alias, $callback);
    }


    public function getGrammar(): Grammar
    {
        return $this->grammar;
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

    public function orderByDesc(string $column): self
    {
        return $this->orderBy($column, 'desc');
    }

    public function orderByAsc(string $column): self
    {
        return $this->orderBy($column, 'asc');
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
            $this->orderBy($this->getTable() . '.created_at', 'desc');
        }
        
        [$sql, $bindings] = $this->grammar->compileSelect($this);
        return $this->connection->select($sql, $bindings);
    }

    public function pluck(string $column, ?string $key = null): array
    {
        $columns = [$column];
        if ($key !== null) {
            $columns[] = $key;
        }

        $this->columns = $columns;

        [$sql, $bindings] = $this->grammar->compileSelect($this);
        $results = $this->connection->select($sql, $bindings);

        if (empty($results)) {
            return [];
        }

        if ($key === null) {
            return array_map(fn($row) => $row->{$column} ?? null, $results);
        }

        $pluck = [];
        foreach ($results as $row) {
            $pluck[$row->{$key}] = $row->{$column};
        }

        return $pluck;
    }



    public function first(): ?object
    {
        $this->limit(1);
        $results = $this->get();
        
        return $results[0] ?? null;
    }

    public function firstOrCreate(array $attributes, array $values = []): object
    {
        $query = clone $this;

        foreach ($attributes as $column => $value) {
            $query->where($column, '=', $value);
        }

        $existing = $query->first();

        if ($existing) {
            return $existing;
        }

        $data = array_merge($attributes, $values);
        $id = $this->insert($data);

        // 새로 삽입된 데이터를 다시 조회 (단일 PK 기준)
        $primaryKey = 'id'; // 필요한 경우 매핑 설정 가능
        return $this->where($primaryKey, '=', $id)->first();
    }


    public function updateOrInsert(array $where, array $values): bool
    {
        $query = clone $this;

        foreach ($where as $column => $value) {
            $query->where($column, '=', $value);
        }

        $exists = $query->first();

        if ($exists) {
            foreach ($where as $column => $value) {
                $this->where($column, '=', $value);
            }
            $this->update($values);
        } else {
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

    public function insertOrIgnore(array $rows): ?int
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

    public function whereColumn(string $first, string $operator, string $second): self
    {
        $this->wheres[] = [
            'type' => 'column',
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'boolean' => 'and',
        ];
        return $this;
    }

    public function orWhereColumn(string $first, string $operator, string $second): self
    {
        $this->wheres[] = [
            'type' => 'column',
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'boolean' => 'or',
        ];
        return $this;
    }

    public function whereHas(string $relation, Closure $callback): self
    {
        return $this->whereHasTyped($relation, $callback, 'and', false);
    }

    public function orWhereHas(string $relation, Closure $callback): self
    {
        return $this->whereHasTyped($relation, $callback, 'or', false);
    }

    public function whereDoesntHave(string $relation, Closure $callback): self
    {
        return $this->whereHasTyped($relation, $callback, 'and', true);
    }

    public function orWhereDoesntHave(string $relation, Closure $callback): self
    {
        return $this->whereHasTyped($relation, $callback, 'or', true);
    }


    protected function whereHasTyped(string $relation, Closure $callback, string $boolean, bool $not): self
    {
        // 💡 이 Builder는 EntityQueryBuilder이므로 repository를 통해 엔티티를 직접 추론 가능
        if (!property_exists($this, 'repository')) {
            throw new \RuntimeException("EntityQueryBuilder must have repository property.");
        }
        
        if (!($this instanceof EntityQueryBuilder)) {
            throw new \RuntimeException("whereHas*()는 EntityQueryBuilder에서만 사용 가능합니다.");
        }

        /** @var class-string<Entity> $entityClass */
        $entityClass = $this->repository::entityClass();
        $entity = new $entityClass();

        $relationObj = Rel::getRelation($entity, $relation);

        if (!$relationObj) {
            throw new \RuntimeException("Relation [{$relation}] is not defined on entity [{$entityClass}].");
        }

        $relatedQuery = $relationObj->getRelatedQuery();
        $callback($relatedQuery);

        $relationObj->addExistsConstraints($relatedQuery, $this);

        $this->wheres[] = [
            'type' => $not ? 'notExists' : 'exists',
            'query' => $relatedQuery,
            'boolean' => $boolean,
        ];

        return $this;
    }


    /**
     * @param string|\Closure $column
     */
    public function where($column, $operator = null, $value = null): self
    {
        // Closure 지원 (서브쿼리, 복합 where)
        if ($column instanceof Closure) {
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

    public function existsBy(array $where): bool
    {
        foreach ($where as $column => $value) {
            $this->where($column, $value);
        }
        return $this->exists();
    }

    
    public function whereIn(string $column, array $values): self
    {
        if (empty($values)) {
            return $this->whereRaw('0 = 1');
        }

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

    public function whereRaw(string $raw, array $bindings = []): self
    {
        $this->wheres[] = [
            'type' => 'raw',
            'sql' => $raw,
            'bindings' => $bindings,
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
