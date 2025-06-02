<?php

namespace Framework\Database\Schema;

class ColumnDefinition
{
    protected string $name;
    protected string $type;
    protected bool $nullable = false;
    protected bool $autoIncrement = false;
    protected bool $unsigned = false;
    protected $default = null;
    protected ?string $onUpdate = null;
    protected array $indexes = [];

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function nullable(): self
    {
        $this->nullable = true;
        return $this;
    }

    public function default($value): self
    {
        $this->default = $value;
        return $this;
    }

    public function onUpdate(string $value): self
    {
        $this->onUpdate = $value;
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
