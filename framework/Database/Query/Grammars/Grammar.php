<?php

namespace Framework\Database\Query\Grammars;

use Framework\Database\Query\Builder;

abstract class Grammar
{
    /**
     * SELECT 쿼리 컴파일
     * @return array{string, array}
     */
    abstract public function compileSelect(Builder $builder): array;

    abstract public function compileInsert(string $table, array $data): array;

    abstract public function compileInsertOrIgnore(string $table, array $rows): array;

    abstract public function compileUpsert(string $table, array $rows, array $uniqueBy, array $updateColumns): array;

    abstract public function compileUpdate(Builder $builder, array $data): array;

    abstract public function compileDelete(Builder $builder): array;
}
