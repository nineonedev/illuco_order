<?php

namespace Framework\Database\Schema;

use Closure;
use Framework\Database\Contracts\ConnectionInterface;

class Schema
{
    protected ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function table(string $table, Closure $callback): void
    {
        $blueprint = new Blueprint($table, true);
        $callback($blueprint);
        $sqls = $blueprint->compileAlter();

        foreach ($sqls as $sql) {
            $this->connection->statement($sql);
        }
    }

    public function create(string $table, Closure $callback): void
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        // 변경: compileCreate()로 외래키 포함 SQL 생성
        $sql = $blueprint->compileCreate();

        $this->connection->statement($sql);
    }

    public function getAllTables(): array
    {
        $tables = [];
        $result = $this->connection->select('SHOW TABLES');

        foreach ($result as $row) {
            // SHOW TABLES 결과는 ["Tables_in_데이터베이스"] 혹은 첫 번째 값만 있음
            $tables[] = array_values((array) $row)[0];
        }
        return $tables;
    }

    public function truncateAllTables(): void
    {
        foreach ($this->getAllTables() as $table) {
            $this->truncateTable($table);
        }
    }

    public function truncateTable(string $table): void
    {
        $this->connection->statement("TRUNCATE TABLE `$table`");
    }

    public function drop(string $table): void
    {
        $this->connection->statement("DROP TABLE `{$table}`");
    }

    public function dropIfExists(string $table): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS `{$table}`");
    }

    public function rename(string $from, string $to): void
    {
        $this->connection->statement("RENAME TABLE `{$from}` TO `{$to}`");
    }

    public function hasTable(string $table): bool
    {
        $table = addslashes($table);

        $result = $this->connection->select(
            "SHOW TABLES LIKE '{$table}'"
        );

        return count($result) > 0;
    }

    public function hasColumn(string $table, string $column): bool
    {
        $table = addslashes($table);
        $column = addslashes($column);

        $result = $this->connection->select(
            "SHOW COLUMNS FROM `{$table}` LIKE '{$column}'"
        );

        return count($result) > 0;
    }

    public function disableForeignKeyChecks(): void
    {
        $this->connection->statement('SET FOREIGN_KEY_CHECKS = 0');
    }

    public function enableForeignKeyChecks(): void
    {
        $this->connection->statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function freshAllTables(callable $callback): void
    {
        $this->disableForeignKeyChecks();
        foreach ($this->getAllTables() as $table) {
            $this->dropIfExists($table);
            $callback($this, $table);
        }
        $this->enableForeignKeyChecks();
    }
}
