<?php

use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Contracts\RepositoryInterface;
use Framework\Database\Database;
use Framework\Database\Schema\Schema;
use Framework\Database\TransactionManager;
use Framework\Database\Entities\Entity;
use Framework\Database\Query\Builder;

// 기본 DB Connection (low-level)
if (!function_exists('connection')) {
    function connection(): ConnectionInterface
    {
        return app(ConnectionInterface::class);
    }
}

// Database 고수준 접근자 (table, schema, transaction 등)
if (!function_exists('database')) {
    function database(): Database
    {
        return app(Database::class);
    }
}

if (!function_exists('repository')) {
    function repository(string $repository): RepositoryInterface
    {
        return app($repository);
    }
}

if (!function_exists('db')) {
    /**
     * @return Builder
     */
    function db(?string $table = null): Builder
    {
        return database()->table($table); 
    }
}

// 스키마 빌더
if (!function_exists('schema')) {
    function schema(?string $connection = null): Schema
    {
        return database()->schema($connection);
    }
}

// 트랜잭션 처리기
if (!function_exists('transaction')) {
    function transaction(callable $callback, ?string $connection = null)
    {
        return database()->transaction($callback, $connection);
    }
}

// repository() : 리포지토리 resolve
if (!function_exists('repository')) {
    function repository(string $entityClass): RepositoryInterface
    {
        return app("repository:{$entityClass}");
    }
}

// entity() : 새로운 엔터티 인스턴스 생성
if (!function_exists('entity')) {
    function entity(string $class, array $attributes = []): Entity
    {
        return new $class($attributes);
    }
}

if (!function_exists('db_select')) {
    function db_select(string $sql, array $bindings = [], ?string $connection = null): array
    {
        return connection()->select($sql, $bindings);
    }
}

if (!function_exists('db_statement')) {
    function db_statement(string $sql, array $bindings = [], ?string $connection = null): bool
    {
        return connection()->statement($sql, $bindings);
    }
}
