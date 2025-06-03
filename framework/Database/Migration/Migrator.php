<?php

namespace Framework\Database\Migration;

use Framework\Console\UI\ProgressBar;
use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Contracts\Migration;
use RuntimeException;

class Migrator
{
    protected MigrationRepository $repository;
    protected ConnectionInterface $connection;
    protected string $migrationPath;

    public function __construct(
        MigrationRepository $repository,
        ConnectionInterface $connection,
        string $migrationPath
    ) {
        $this->repository = $repository;
        $this->connection = $connection;
        $this->migrationPath = rtrim($migrationPath, '/');
    }

    public function migrate(): void
    {
        $this->repository->ensureMigrationTableExists($this->connection);

        $ran = $this->repository->getRan();
        $files = MigrationFile::all($this->migrationPath);
        $pending = array_filter($files, fn($file) => !in_array($file, $ran, true)); 

        $bar = new ProgressBar(count($pending), 'Migrating', 'done'); 
        $bar->advance(0);

        foreach ($files as $file) {
            $migration = require $file;
            

            if (! $migration instanceof Migration) {
                throw new RuntimeException("Migration must return instance of Migration.");
            }

            $this->connection->beginTransaction();
            $migration->up();
            $this->repository->log($file);
            $this->connection->commit();

            $bar->advance(); 
        }

        $bar->finish();
    }

    public function rollback(): void
    {
        $lastBatch = $this->repository->getLastBatch();

        if (empty($lastBatch)) {
            echo "Nothing to rollback.\n";
            return;
        }

        foreach ($lastBatch as $migration) {
            require_once "{$this->migrationPath}/{$migration['name']}.php";

            $class = MigrationFile::classFromFile($migration['name']);
            $instance = new $class();

            if (!method_exists($instance, 'down')) {
                throw new RuntimeException("Migration class [{$class}] must have method [down]");
            }

            $this->connection->beginTransaction();

            $instance->down();

            $this->repository->delete($migration['name']);

            $this->connection->commit();
        }
    }

    public function fresh(): void
    {
        $this->repository->drop();
        $this->migrate();
    }
}
