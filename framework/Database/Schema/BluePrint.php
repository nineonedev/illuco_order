<?php

namespace Framework\Database\Schema;

use Framework\Database\Schema\ColumnDefinition;

class Blueprint
{
    protected string $table;
    /** @var ColumnDefinition[] */
    protected array $columns = [];

    /** @var array<string, mixed> */
    protected array $commands = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function id(string $name = 'id'): ColumnDefinition
    {
        return $this->addColumn('INT', $name)
            ->unsigned()
            ->autoIncrement()
            ->primary();
    }

    public function string(string $name, int $length = 255): ColumnDefinition
    {
        return $this->addColumn("VARCHAR($length)", $name);
    }

    public function text(string $name): ColumnDefinition
    {
        return $this->addColumn('TEXT', $name);
    }

    public function boolean(string $name): ColumnDefinition
    {
        return $this->addColumn('TINYINT(1)', $name);
    }

    public function integer(string $name): ColumnDefinition
    {
        return $this->addColumn('INT', $name);
    }

    public function bigInteger(string $name): ColumnDefinition
    {
        return $this->addColumn('BIGINT', $name);
    }

    public function float(string $name, int $total = 8, int $places = 2): ColumnDefinition
    {
        return $this->addColumn("FLOAT($total, $places)", $name);
    }

    public function double(string $name, int $total = 8, int $places = 2): ColumnDefinition
    {
        return $this->addColumn("DOUBLE($total, $places)", $name);
    }

    public function decimal(string $name, int $precision = 10, int $scale = 2): ColumnDefinition
    {
        return $this->addColumn("DECIMAL($precision, $scale)", $name);
    }

    public function date(string $name): ColumnDefinition
    {
        return $this->addColumn('DATE', $name);
    }

    public function datetime(string $name): ColumnDefinition
    {
        return $this->addColumn('DATETIME', $name);
    }

    public function timestamp(string $name): ColumnDefinition
    {
        return $this->addColumn('TIMESTAMP', $name);
    }

    public function time(string $name): ColumnDefinition
    {
        return $this->addColumn('TIME', $name);
    }

    public function year(string $name): ColumnDefinition
    {
        return $this->addColumn('YEAR', $name);
    }

    public function enum(string $name, array $values): ColumnDefinition
    {
        $escaped = array_map(fn($v) => "'$v'", $values);
        return $this->addColumn('ENUM(' . implode(',', $escaped) . ')', $name);
    }

    public function json(string $name): ColumnDefinition
    {
        return $this->addColumn('JSON', $name);
    }

    public function binary(string $name): ColumnDefinition
    {
        return $this->addColumn('BLOB', $name);
    }

    public function mediumText(string $name): ColumnDefinition
    {
        return $this->addColumn('MEDIUMTEXT', $name);
    }

    public function longText(string $name): ColumnDefinition
    {
        return $this->addColumn('LONGTEXT', $name);
    }

    public function char(string $name, int $length = 255): ColumnDefinition
    {
        return $this->addColumn("CHAR($length)", $name);
    }

    public function set(string $name, array $values): ColumnDefinition
    {
        $escaped = array_map(fn($v) => "'$v'", $values);
        return $this->addColumn('SET(' . implode(',', $escaped) . ')', $name);
    }

    public function timestamps(): void
    {
        $this->datetime('created_at')->default('CURRENT_TIMESTAMP');
        $this->datetime('updated_at')->default('CURRENT_TIMESTAMP')->onUpdate('CURRENT_TIMESTAMP');
    }

    public function softDeletes(string $column = 'deleted_at'): void
    {
        $this->datetime($column)->nullable();
    }

    public function primaryKey(string ...$columns): void
    {
        $this->commands['primary'] = $columns;
    }

    public function dropColumn(string ...$columns): void
    {
        $this->commands['drop'][] = $columns;
    }

    public function renameColumn(string $from, string $to): void
    {
        $this->commands['rename'][$from] = $to;
    }

    protected function addColumn(string $type, string $name): ColumnDefinition
    {
        $column = new ColumnDefinition($name, $type);
        $this->columns[] = $column;
        return $column;
    }

    public function hasColumn(string $name): bool
    {
        foreach ($this->columns as $col) {
            if ($col->getName() === $name) return true;
        }
        return false;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }
}
