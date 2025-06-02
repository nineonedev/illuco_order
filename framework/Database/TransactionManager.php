<?php

namespace Framework\Database;

use Framework\Database\Contracts\ConnectionInterface;
use Throwable;

class TransactionManager
{
    protected ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function begin(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollBack(): void
    {
        $this->connection->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->connection->inTransaction();
    }

    public function run(callable $callback)
    {
        $this->begin();

        try {
            $result = $callback();
            $this->commit();

            return $result;
        } catch (Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }
}
