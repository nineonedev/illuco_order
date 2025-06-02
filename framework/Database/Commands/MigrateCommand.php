<?php

namespace Framework\Database\Commands;

use Framework\Console\Command;
use Framework\Database\Migrations\MigrationRunner;

class MigrateCommand extends Command
{
    protected string $signature = 'migrate';
    protected string $description = 'Run the database migrations';

    public function handle(): void
    {
        $runner = app(MigrationRunner::class); 
        $runner->migrate(); 

        $this->info('Migrations run successfully.'); 
    }
}
