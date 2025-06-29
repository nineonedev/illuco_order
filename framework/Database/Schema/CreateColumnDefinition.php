<?php

namespace Framework\Database\Schema;

class CreateColumnDefinition extends AbstractColumnDefinition
{
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
            $parts[] = 'DEFAULT NULL';
        }

        if ($this->autoIncrement) {
            $parts[] = 'AUTO_INCREMENT';
        }

        if ($this->onUpdate) {
            $parts[] = 'ON UPDATE ' . $this->formatDefault($this->onUpdate);
        }

        if ($this->comment !== null) {
            $parts[] = "COMMENT '{$this->comment}'";
        }

        return implode(' ', $parts);
    }
}
