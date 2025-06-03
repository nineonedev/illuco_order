<?php

namespace Framework\Support\Facades;

use Framework\Database\Database;

/**
 * @method static \Framework\Database\Contracts\ConnectionInterface connection(?string $name = null)
 * @method static \Framework\Database\Query\Builder table(string $table, ?string $connection = null)
 * @method static \Framework\Database\Schema\Schema schema(?string $connection = null)
 * @method static mixed transaction(\Closure $callback, ?string $connection = null)
 * @method static array select(string $sql, array $bindings = [], ?string $connection = null)
 * @method static bool statement(string $sql, array $bindings = [], ?string $connection = null)
 *
 * @see \Framework\Database\Database
 */
class DB extends Facade
{
    /**
     * 이 파사드는 Database 클래스를 정적으로 접근하기 위한 헬퍼입니다.
     *
     * 주로 DB::table(), DB::transaction() 등과 같은 방식으로 사용됩니다.
     */
    protected static function getFacadeAccessor(): string
    {
        return Database::class;
    }
}
