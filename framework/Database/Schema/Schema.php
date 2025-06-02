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

    public function create(string $table, Closure $callback): void
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $columnsSql = [];
        $primaryKeys = [];
        $uniqueIndexes = [];
        $normalIndexes = [];

        foreach ($blueprint->getColumns() as $column) {
            $columnsSql[] = $column->toSql();

            foreach ($column->getIndexes() as $index) {
                $name = $column->getName();
                if ($index === 'PRIMARY') {
                    $primaryKeys[] = "`$name`";
                } elseif ($index === 'UNIQUE') {
                    $uniqueIndexes[] = "UNIQUE (`$name`)";
                } elseif ($index === 'INDEX') {
                    $normalIndexes[] = "INDEX (`$name`)";
                }
            }
        }

        $sql = "CREATE TABLE `{$table}` (\n";
        $sql .= implode(",\n", $columnsSql);

        if (!empty($primaryKeys)) {
            $sql .= ",\nPRIMARY KEY (" . implode(', ', $primaryKeys) . ")";
        }

        if (!empty($uniqueIndexes)) {
            $sql .= ",\n" . implode(",\n", $uniqueIndexes);
        }

        if (!empty($normalIndexes)) {
            $sql .= ",\n" . implode(",\n", $normalIndexes);
        }

        $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->connection->statement($sql);
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

}
