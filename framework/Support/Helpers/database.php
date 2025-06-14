<?php

use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Database;
use Framework\Database\ORM\Repositories\Repository;
use Framework\Database\Schema\Schema;
use Framework\Database\Query\Builder;
use Framework\Database\Query\EntityQueryBuilder;
use Framework\Database\TransactionManager;

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

if (!function_exists('query')) {
    function query(Repository $repository): EntityQueryBuilder
    {
        return database()->query($repository);
    }
}

if (!function_exists('db')) {
    /**
     * @return Database|Builder
     */
    function db(?string $table = null)
    {
        if (is_null($table)) {
            /** @return Database */
            return database();
        }

        /** @return Builder */
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
    function transaction(): TransactionManager
    {
        return database()->transaction();
    }
}

if (!function_exists('db_select')) {
    function db_select(string $sql, array $bindings = []): array
    {
        return connection()->select($sql, $bindings);
    }
}

if (!function_exists('db_statement')) {
    function db_statement(string $sql, array $bindings = []): bool
    {
        return connection()->statement($sql, $bindings);
    }
}
