<?php

namespace Framework\Database\Commands;

use Framework\Console\Command;
use Framework\Database\Migrations\MigrationRunner;

class RollbackCommand extends Command
{
    protected string $signature = 'migrate:rollback';
    protected string $description = 'Rollback the last database migration';

    public function handle(): void
    {
        $runner = app(MigrationRunner::class);
        $runner->rollback();

        $this->info('Last migration rolled back!');
    }
}
