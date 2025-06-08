<?php

namespace Framework\Database\Schema;

class AlterColumnDefinition extends AbstractColumnDefinition
{
    protected ?string $newName = null;
    protected string $action;

    public function __construct(string $name, string $action = 'MODIFY', string $type = null)
    {
        parent::__construct($name, $type ?? '');
        $this->action = strtoupper($action);
    }

    public static function add(string $name, string $type): self
    {
        return new self($name, 'ADD', $type);
    }

    public static function modify(string $name, string $type): self
    {
        return new self($name, 'MODIFY', $type);
    }

    public static function drop(string $name): self
    {
        return new self($name, 'DROP');
    }

    public static function rename(string $from, string $to): self
    {
        $def = new self($from, 'RENAME');
        $def->newName = $to;
        return $def;
    }

    public static function foreign(string $column, ?string $name = null): self
    {
        $def = new self($column, 'FOREIGN');
        $def->foreignReferences = 'id';
        $def->options['name'] = $name;
        return $def;
    }

    public function toSql(): string
    {
        switch ($this->action) {
            case 'ADD':
                return "ADD COLUMN " . $this->buildColumnSql($this->name, $this->type);
            case 'MODIFY':
                return "MODIFY COLUMN " . $this->buildColumnSql($this->name, $this->type);
            case 'RENAME':
                return "RENAME COLUMN `{$this->name}` TO `{$this->newName}`";
            case 'DROP':
                return "DROP COLUMN `{$this->name}`";
            case 'FOREIGN':
                $fkName = $this->options['name'] ?? "fk_{$this->name}";
                $onDelete = $this->onDelete ? " ON DELETE {$this->onDelete}" : '';
                $onUpdate = $this->onUpdate ? " ON UPDATE {$this->onUpdate}" : '';
                return "ADD CONSTRAINT `{$fkName}` FOREIGN KEY (`{$this->name}`) REFERENCES `{$this->foreignOn}` (`{$this->foreignReferences}`){$onDelete}{$onUpdate}";
            default:
                throw new \RuntimeException("Unknown ALTER COLUMN action: {$this->action}");
        }
    }

    protected function buildColumnSql(string $name, string $type): string
    {
        $parts = ["`{$name}` {$type}"];
        if ($this->unsigned) {
            $parts[] = 'UNSIGNED';
        }
        if ($this->nullable !== null) {
            $parts[] = $this->nullable ? 'NULL' : 'NOT NULL';
        }
        if ($this->default !== null) {
            $parts[] = 'DEFAULT ' . $this->formatDefault($this->default);
        }
        if ($this->autoIncrement) {
            $parts[] = 'AUTO_INCREMENT';
        }
        return implode(' ', $parts);
    }
}
