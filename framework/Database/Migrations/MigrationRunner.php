<?php

namespace Framework\Database\Migrations;

use Framework\Database\Contracts\Migration;
use Framework\Database\Contracts\ConnectionInterface;

class MigrationRunner
{
    protected MigrationRepository $repository;
    protected ConnectionInterface $connection;
    protected string $path;

    public function __construct(
        MigrationRepository $repository,
        ConnectionInterface $connection,
        string $path = 'database/migrations'
    ) {
        $this->repository = $repository;
        $this->connection = $connection;
        $this->path = $path;
    }

    /**
     * 마이그레이션을 모두 실행
     */
    public function migrate(): void
    {
        $ran = $this->repository->getRan();

        foreach ($this->getMigrationFiles() as $file) {
            $name = basename($file, '.php');

            if (in_array($name, $ran)) {
                continue; // 이미 실행됨
            }

            $migration = $this->resolve($file);
            $migration->up();

            $this->repository->log($name);
            echo "Migrated: {$name}\n";
        }
    }

    /**
     * 마지막 실행된 마이그레이션을 롤백
     */
    public function rollback(): void
    {
        $ran = $this->repository->getRan();

        if (empty($ran)) {
            echo "Nothing to rollback.\n";
            return;
        }

        $last = array_pop($ran);
        $file = $this->path . '/' . $last . '.php';

        if (!file_exists($file)) {
            echo "Migration file not found: {$file}\n";
            return;
        }

        $migration = $this->resolve($file);
        $migration->down();

        $this->repository->delete($last);
        echo "Rolled back: {$last}\n";
    }

    /**
     * 마이그레이션 파일 목록 가져오기
     */
    protected function getMigrationFiles(): array
    {
        return glob($this->path . '/*.php') ?: [];
    }

    /**
     * 마이그레이션 파일에서 클래스 인스턴스 생성
     */
    protected function resolve(string $file): Migration
    {
        require_once $file;

        // 파일명과 같은 클래스가 있다고 가정
        $class = basename($file, '.php');

        if (!class_exists($class)) {
            throw new \RuntimeException("Migration class [{$class}] not found.");
        }

        $instance = new $class();

        if (!$instance instanceof Migration) {
            throw new \RuntimeException("Migration class [{$class}] must implement Migration interface.");
        }

        return $instance;
    }
}
