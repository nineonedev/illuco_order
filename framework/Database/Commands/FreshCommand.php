<?php

namespace Framework\Database\Commands;

use Framework\Console\Command;
use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Migrations\MigrationRunner;

class FreshCommand extends Command
{
    protected string $signature = 'migrate:fresh';
    protected string $description = 'Drop all tables and re-run all migrations';

    public function handle(): void
    {
        $schema = app(ConnectionInterface::class)->schema();

        // 전체 테이블 드롭
        foreach ($schema->getTables() as $table) {
            $schema->drop($table);
        }

        // 다시 migrate
        app(MigrationRunner::class)->migrate();

        $this->info('Database refreshed!');
    }
}
