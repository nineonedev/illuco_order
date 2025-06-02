<?php

namespace Framework\Database;

use Closure;
use Framework\Database\Query\Builder;
use Framework\Database\Query\Grammars\MysqlGrammar;
use Framework\Database\Schema\Schema;
use Framework\Database\TransactionManager;
use Framework\Database\Contracts\ConnectionInterface;

class Database
{
    protected DatabaseManager $manager;

    public function __construct(DatabaseManager $manager)
    {
        $this->manager = $manager;
    }

    public function connection(?string $name = null): ConnectionInterface
    {
        return $this->manager->connection($name);
    }

    public function table(string $table, ?string $connection = null): Builder
    {
        return new Builder(
            $this->connection($connection),
            new MysqlGrammar(),
            $table
        );
    }

    public function schema(?string $connection = null): Schema
    {
        return $this->manager->connection($connection)->schema();
    }

    public function transaction(Closure $callback, ?string $connection = null)
    {
        $tx = new TransactionManager($this->connection($connection));
        return $tx->run($callback);
    }

    public function select(string $sql, array $bindings = [], ?string $connection = null): array
    {
        return $this->connection($connection)->select($sql, $bindings);
    }

    public function statement(string $sql, array $bindings = [], ?string $connection = null): bool
    {
        return $this->connection($connection)->statement($sql, $bindings);
    }
}
