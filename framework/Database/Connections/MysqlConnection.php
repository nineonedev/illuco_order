<?php

namespace Framework\Database\Connections;

use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Query\Builder;
use Framework\Database\Query\Grammars\MysqlGrammar;
use Framework\Database\Schema\Schema;
use PDO;
use PDOException;

class MysqlConnection implements ConnectionInterface {
    protected PDO $pdo; 
    protected MysqlGrammar $grammar; 

    public function __construct(array $config = [
        'host' => '127.0.0.1',
        'database' => '',
        'port' => 3306,
        'charset' => 'utf8mb4',
        'username' => '',
        'password' => '',
    ])
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;port=%d;charset=%s',
            $config['host'],
            $config['database'],
            $config['port'],
            $config['charset'],
        );
    
        try {
            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new \RuntimeException("MySQL Connection failed: ". $e->getMessage()); 
        }

        $this->grammar = new MysqlGrammar();
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    public function trasaction(callable $callback)
    {
        try {
            $this->beginTransaction();
            $result = $callback();
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($this->inTransaction()) {
                $this->rollback();
            }
            throw $e;
        }
    }

    public function table(string $table): Builder
    {
        return new Builder($this, $this->grammar, $table); 
    }

    public function select(string $query, array $bindings = []): array
    {
        $stmt = $this->pdo->prepare($query); 
        $stmt->execute($bindings); 
        return $stmt->fetchAll();
    }

    public function insert(string $query, array $bindings = []): ?int
    {
        $stmt = $this->pdo->prepare($query);
        $ok = $stmt->execute($bindings);
        if ($ok) {
            $id = $this->pdo->lastInsertId();
            return $id ? (int)$id : null;
        }
        return null;
    }

    public function update(string $query, array $bindings = []): int
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($bindings); 
        return $stmt->rowCount();
    }
    
    public function delete(string $query, array $bindings = []): int
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($bindings); 
        return $stmt->rowCount();
    }

    public function statement(string $query, array $bindings = []): bool
    {
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($bindings); 
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollback();
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    public function lastInsertId(): ?int
    {
        return (int) $this->pdo->lastInsertId();
    }

    public function upsert(string $sql, array $bindings): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    public function insertOrIgnore(string $sql, array $bindings): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    public function schema(): Schema
    {
        return new Schema($this); 
    }
}
