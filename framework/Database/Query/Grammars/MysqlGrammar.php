<?php

namespace Framework\Database\Query\Grammars;

use Framework\Database\Query\Builder;

class MysqlGrammar extends Grammar
{
    public function compileSelect(Builder $builder): array
    {
        $sql = 'select ' . implode(', ', $builder->getColumns()) .
               ' from `' . $builder->getTable() . '`';

        $bindings = [];

        // JOIN
        foreach ($builder->getJoins() as $join) {
            $sql .= " {$join['type']} join `{$join['table']}` on {$join['first']} {$join['operator']} {$join['second']}";
        }

        // WHERE
        if ($wheres = $builder->getWheres()) {
            $sql .= ' where ';
            $parts = [];

            foreach ($wheres as $i => $where) {
                $boolean = $i > 0 ? strtoupper($where['boolean'] ?? 'and') . ' ' : '';
                if ($where['type'] === 'basic') {
                    $parts[] = "{$boolean}`{$where['column']}` {$where['operator']} ?";
                    $bindings[] = $where['value'];
                } elseif ($where['type'] === 'in') {
                    $in = implode(', ', array_fill(0, count($where['values']), '?'));
                    $parts[] = "{$boolean}`{$where['column']}` IN ({$in})";
                    $bindings = array_merge($bindings, $where['values']);
                } elseif ($where['type'] === 'exists') {
                    [$subSql, $subBindings] = $this->compileSelect($where['query']);
                    $parts[] = "{$boolean}EXISTS ({$subSql})";
                    $bindings = array_merge($bindings, $subBindings);
                } elseif ($where['type'] === 'raw') {
                    $parts[] = "{$boolean}{$where['sql']}";
                }
            }

            $sql .= implode(' ', $parts);
        }

        // GROUP BY
        if ($groups = $builder->getGroups()) {
            $sql .= ' group by ' . implode(', ', array_map(fn($g) => "`$g`", $groups));
        }

        // HAVING
        if ($havings = $builder->getHavings()) {
            $sql .= ' having ';
            $parts = [];

            foreach ($havings as $i => $having) {
                $boolean = $i > 0 ? strtoupper($having['boolean'] ?? 'and') . ' ' : '';
                if ($having['type'] === 'basic') {
                    $parts[] = "{$boolean}`{$having['column']}` {$having['operator']} ?";
                    $bindings[] = $having['value'];
                } elseif ($having['type'] === 'raw') {
                    $parts[] = "{$boolean}{$having['sql']}";
                }
            }

            $sql .= implode(' ', $parts);
        }

        // ORDER
        if ($orders = $builder->getOrders()) {
            $sql .= ' order by ' . implode(', ', array_map(
                fn($o) => "`{$o['column']}` " . strtoupper($o['direction']),
                $orders
            ));
        }

        // LIMIT/OFFSET
        if ($builder->getLimit()) {
            $sql .= ' limit ' . $builder->getLimit();
        }
        if ($builder->getOffset()) {
            $sql .= ' offset ' . $builder->getOffset();
        }

        // UNION
        foreach ($builder->getUnions() as $union) {
            [$unionSql, $unionBindings] = $this->compileSelect($union['query']);
            $sql .= ' ' . ($union['all'] ? 'union all' : 'union') . " ({$unionSql})";
            $bindings = array_merge($bindings, $unionBindings);
        }

        return [$sql, $bindings];
    }

    public function compileInsert(string $table, array $data): array
    {
        $columns = array_keys($data);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $sql = "insert into `{$table}` (" . implode(', ', array_map(fn($c) => "`$c`", $columns)) . ") values ({$placeholders})";

        return [$sql, array_values($data)];
    }

    public function compileInsertOrIgnore(string $table, array $rows): array
    {
        $columns = array_keys($rows[0]);
        $placeholders = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
        $sql = "insert ignore into `{$table}` (" . implode(', ', array_map(fn($c) => "`$c`", $columns)) . ") values ";
        $sql .= implode(', ', array_fill(0, count($rows), $placeholders));

        $bindings = [];
        foreach ($rows as $row) {
            foreach ($columns as $col) {
                $bindings[] = $row[$col];
            }
        }

        return [$sql, $bindings];
    }

    public function compileUpsert(string $table, array $rows, array $uniqueBy, array $updateColumns): array
    {
        $columns = array_keys($rows[0]);
        $placeholders = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
        $sql = "insert into `{$table}` (" . implode(', ', array_map(fn($c) => "`$c`", $columns)) . ") values ";
        $sql .= implode(', ', array_fill(0, count($rows), $placeholders));
        $sql .= " on duplicate key update ";
        $sql .= implode(', ', array_map(fn($c) => "`$c` = values(`$c`)", $updateColumns));

        $bindings = [];
        foreach ($rows as $row) {
            foreach ($columns as $col) {
                $bindings[] = $row[$col];
            }
        }

        return [$sql, $bindings];
    }

    public function compileUpdate(Builder $builder, array $data): array
    {
        $sql = "update `{$builder->getTable()}` set ";
        $set = implode(', ', array_map(fn($k) => "`$k` = ?", array_keys($data)));
        $sql .= $set;

        $bindings = array_values($data);

        if ($wheres = $builder->getWheres()) {
            $sql .= ' where ';
            $parts = [];

            foreach ($wheres as $where) {
                $parts[] = "`{$where['column']}` {$where['operator']} ?";
                $bindings[] = $where['value'];
            }

            $sql .= implode(' and ', $parts);
        }

        return [$sql, $bindings];
    }

    public function compileDelete(Builder $builder): array
    {
        $sql = "delete from `{$builder->getTable()}`";
        $bindings = [];

        if ($wheres = $builder->getWheres()) {
            $sql .= ' where ';
            $parts = [];

            foreach ($wheres as $where) {
                $parts[] = "`{$where['column']}` {$where['operator']} ?";
                $bindings[] = $where['value'];
            }

            $sql .= implode(' and ', $parts);
        }

        return [$sql, $bindings];
    }
}
