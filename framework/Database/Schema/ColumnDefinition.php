<?php

namespace Framework\Database\Schema;

class ColumnDefinition
{

    protected ?string $foreignTable = null;
    protected ?string $foreignColumn = null;
    protected ?string $onDelete = null;
    protected ?string $onUpdate = null;

    protected string $name;
    protected string $type;
    protected bool $nullable = false;
    protected bool $autoIncrement = false;
    protected bool $unsigned = false;
    protected $default = null;
    protected array $indexes = [];

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function foreign(): self
    {
        return $this;
    }

    public function references(string $column): self
    {
        $this->foreignColumn = $column;
        return $this;
    }

    public function on(string $table): self
    {
        $this->foreignTable = $table;
        return $this;
    }

    public function onDelete(string $action): self
    {
        $this->onDelete = strtoupper($action);
        return $this;
    }

    public function onUpdate(string $action): self
    {
        $this->onUpdate = strtoupper($action);
        return $this;
    }

    public function onDeleteCascade(): self
    {
        return $this->onDelete('CASCADE');
    }

    public function onDeleteSetNull(): self
    {
        return $this->onDelete('SET NULL');
    }

    public function onDeleteRestrict(): self
    {
        return $this->onDelete('RESTRICT');
    }

    public function onDeleteNoAction(): self
    {
        return $this->onDelete('NO ACTION');
    }

    public function onUpdateCascade(): self
    {
        return $this->onUpdate('CASCADE');
    }

    public function onUpdateSetNull(): self
    {
        return $this->onUpdate('SET NULL');
    }

    public function onUpdateRestrict(): self
    {
        return $this->onUpdate('RESTRICT');
    }

    public function onUpdateNoAction(): self
    {
        return $this->onUpdate('NO ACTION');
    }

    public function getForeign(): ?array
    {
        if (! $this->foreignTable || ! $this->foreignColumn) {
            return null;
        }

        return [
            'column' => $this->name,
            'references' => $this->foreignColumn,
            'on' => $this->foreignTable,
            'onDelete' => $this->onDelete,
            'onUpdate' => $this->onUpdate,
        ];
    }

    public function nullable(bool $nullable = true): self
    {
        $this->nullable = $nullable;
        return $this;
    }

    public function default($value): self
    {
        $this->default = $value;
        return $this;
    }

    public function autoIncrement(): self
    {
        $this->autoIncrement = true;
        return $this;
    }

    public function unsigned(): self
    {
        $this->unsigned = true;
        return $this;
    }

    public function primary(): self
    {
        $this->indexes[] = 'PRIMARY';
        return $this;
    }

    public function unique(): self
    {
        $this->indexes[] = 'UNIQUE';
        return $this;
    }

    public function index(): self
    {
        $this->indexes[] = 'INDEX';
        return $this;
    }

    public function toSql(): string
    {
        $parts = ["`{$this->name}` {$this->type}"];

        if ($this->unsigned) {
            $parts[] = 'UNSIGNED';
        }

        $parts[] = $this->nullable ? 'NULL' : 'NOT NULL';

        if ($this->default !== null) {
            $parts[] = 'DEFAULT ' . $this->formatDefault($this->default);
        } elseif ($this->nullable) {
            // 명시적 DEFAULT NULL 추가 (MySQL 정확성 보완)
            $parts[] = 'DEFAULT NULL';
        }

        if ($this->autoIncrement) {
            $parts[] = 'AUTO_INCREMENT';
        }

        if ($this->onUpdate) {
            $parts[] = 'ON UPDATE ' . $this->formatDefault($this->onUpdate);
        }

        return implode(' ', $parts);
    }

    protected function formatDefault($value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_string($value) && strtoupper($value) === 'CURRENT_TIMESTAMP') {
            return 'CURRENT_TIMESTAMP';
        }

        return is_numeric($value) ? (string)$value : "'$value'";
    }

    public function getIndexes(): array
    {
        return $this->indexes;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
