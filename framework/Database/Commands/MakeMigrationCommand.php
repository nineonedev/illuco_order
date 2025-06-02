<?php

namespace Framework\Database\Commands;

use Framework\Console\Command;
use Framework\Database\Migrations\MigrationCreator;

class MakeMigrationCommand extends Command
{
    protected string $signature = 'make:migration {name} {--table=}';
    protected string $description = 'Create a new migration file';

    protected MigrationCreator $creator;

    public function __construct()
    {
        $this->creator = new MigrationCreator();
    }

    public function handle(): void
    {
        $name = $this->getArgument('name');
        $table = $this->getOption('table');

        if (!$name) {
            $this->error('Please provide a migration name.');
            return;
        }

        $file = $this->creator->create($name, $table);
        $this->info("Migration created: {$file}");
    }
}
