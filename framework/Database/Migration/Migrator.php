<?php

namespace Framework\Database\Migration;

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


        foreach ($files as $file) {
            if (in_array($file, $ran, true)) {
                continue;
            }

            $migration = require $file;  

            if (! $migration instanceof Migration) {
                throw new RuntimeException("Migration must return instance of Migration.");
            }

            $this->connection->beginTransaction();

            $migration->up($this->connection->schema());

            $this->repository->log($file);

            $this->connection->commit();
        }
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

            $instance->down($this->connection->schema());

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
