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
        $this->repository->ensureMigrationTableExists();

        $ran = $this->repository->getRan(); // ex: ['20240604_000000_create_users_table']
        $files = MigrationFile::all($this->migrationPath); // ex: ['/path/to/20240604_000000_create_users_table.php']

        $pending = array_filter($files, function ($file) use ($ran) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            return !in_array($filename, $ran, true);
        });

        $bar = new ProgressBar(count($pending), 'Migrating', 'done');
        $bar->advance(0);

        foreach ($pending as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $migration = require $file;

            if (! $migration instanceof Migration) {
                throw new RuntimeException("Migration must return instance of Migration.");
            }

            $this->connection->beginTransaction();
            $migration->up();
            $this->repository->log($filename);
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
            $path = "{$this->migrationPath}/{$migration->name}.php";

            if (!file_exists($path)) {
                throw new RuntimeException("Migration file not found: {$path}");
            }

            $instance = require $path;

            if (! $instance instanceof Migration) {
                throw new RuntimeException("Migration must return instance of Migration.");
            }

            $this->connection->beginTransaction();
            try {
                $instance->down();
                $this->repository->delete($migration->name);
                $this->connection->commit();
            } catch (\Throwable $e) {
                $this->connection->rollback();
                throw $e; 
            }
        }
    }

    public function fresh(): void
    {
        $this->repository->drop();
        $this->migrate();
    }
}
