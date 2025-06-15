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
        $sql = $blueprint->compileCreate();

        $this->connection->statement($sql);
    }

    public function getAllTables(): array
    {
        $tables = [];
        $result = $this->connection->select('SHOW TABLES');

        foreach ($result as $row) {
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

    /**
     * 지정된 테이블의 모든 컬럼명 배열 반환
     * 예: ['id', 'role_id', 'permission_id', 'resource', 'action', ...]
     */
    public function getColumnListing(string $table): array
    {
        $columns = [];
        $result = $this->connection->select("SHOW COLUMNS FROM `{$table}`");

        foreach ($result as $row) {
            $columns[] = $row->Field; // MySQL 전용
        }

        return $columns;
    }

    /**
     * 지정된 테이블의 컬럼명 + 데이터 타입 반환
     * 예: ['id' => 'int(11)', 'name' => 'varchar(255)', ...]
     */
    public function getColumnTypes(string $table): array
    {
        $types = [];
        $result = $this->connection->select("SHOW COLUMNS FROM `{$table}`");

        foreach ($result as $row) {
            $types[$row->Field] = $row->Type;
        }

        return $types;
    }
}
