<?php

namespace Framework\Database\Contracts;

use Framework\Database\Query\Builder;
use Framework\Database\Schema\Schema;
use PDO;

interface ConnectionInterface
{
    public function pdo(): PDO;

    public function table(string $table): Builder;

    public function select(string $query, array $bindings = []): array;

    public function insert(string $query, array $bindings = []): bool;

    public function update(string $query, array $bindings = []): int;

    public function delete(string $query, array $bindings = []): int;

    public function statement(string $query, array $bindings = []): bool;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;

    public function inTransaction(): bool;

    public function lastInsertId(): ?int;

    public function upsert(string $sql, array $bindings): int;

    public function insertOrIgnore(string $sql, array $bindings): int;

    public function schema(): Schema;
}
