<?php

namespace Framework\Database\Query;

class SubQuery
{
    public Builder $query;
    public string $alias;

    public function __construct(Builder $query, string $alias)
    {
        $this->query = $query;
        $this->alias = $alias;
    }

    public function toSql(): string
    {
        [$sql] = $this->query->getGrammar()->compileSelect($this->query);
        return "({$sql}) as `{$this->alias}`";
    }

    public function getBindings(): array
    {
        return $this->query->getBindings();
    }
}
