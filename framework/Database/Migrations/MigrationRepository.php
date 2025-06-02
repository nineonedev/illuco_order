<?php

namespace Framework\Database\Migrations;

use Framework\Database\Contracts\ConnectionInterface;


class MigrationRepository
{
    protected ConnectionInterface $connection;

    protected string $table = 'migrations';

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->ensureTableExists();
    }

    /**
     * 마이그레이션 이력 테이블이 없으면 생성
     */
    protected function ensureTableExists(): void
    {
        if (!$this->connection->schema()->hasTable($this->table)) {
            $this->connection->schema()->create($this->table, function ($table) {
                $table->id();
                $table->string('migration');
                $table->datetime('created_at')->default('CURRENT_TIMESTAMP');
            });
        }
    }

    /**
     * 실행된 마이그레이션 목록 가져오기
     */
    public function getRan(): array
    {
        $rows = $this->connection
            ->table($this->table)
            ->orderBy('id')
            ->pluck('migration');

        return $rows;
    }

    /**
     * 실행된 마이그레이션으로 등록
     */
    public function log(string $migration): void
    {
        $this->connection
            ->table($this->table)
            ->insert(['migration' => $migration]);
    }

    /**
     * 마이그레이션 이력에서 제거
     */
    public function delete(string $migration): void
    {
        $this->connection
            ->table($this->table)
            ->where('migration', '=', $migration)
            ->delete();
    }

    /**
     * 전체 이력 삭제 (테스트용 등)
     */
    public function clear(): void
    {
        $this->connection
            ->table($this->table)
            ->delete();
    }
}
