<?php

namespace Framework\Database\Migration;

use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Schema\Blueprint;

class MigrationRepository
{
    protected ConnectionInterface $connection;
    protected string $table = 'migrations';

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * 마이그레이션 테이블이 존재하지 않으면 생성
     */
    public function ensureMigrationTableExists(): void
    {
        if (!$this->connection->schema()->hasTable($this->table)) {
            $this->connection->schema()->create($this->table, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('batch');
                $table->timestamps(); // created_at, updated_at 자동 추가
            });
        }
    }

    /**
     * 실행된 마이그레이션 파일 이름들
     */
    public function getRan(): array
    {
        return $this->connection
            ->table($this->table)
            ->select(['name'])
            ->pluck('name');
    }

    /**
     * 마지막 배치의 마이그레이션들
     */
    public function getLastBatch(): array
    {
        $lastBatch = $this->connection
            ->table($this->table)
            ->select(['batch'])
            ->orderBy('batch', 'desc')
            ->first();

        $batch = $lastBatch->batch ?? 0;

        return $this->connection
            ->table($this->table)
            ->select(['name', 'batch', 'created_at'])
            ->where('batch', '=', $batch)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 마이그레이션 실행 기록 저장
     */
    public function log(string $name): void
    {
        $batch = $this->getNextBatchNumber();
        $now = date('Y-m-d H:i:s');

        $this->connection->table($this->table)->insert([
            'name'       => $name,
            'batch'      => $batch,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    protected function getNextBatchNumber(): int
    {
        $latest = $this->connection
            ->table($this->table)
            ->select(['batch'])
            ->orderBy('batch', 'desc')
            ->first();

        return ($latest->batch ?? 0) + 1;
    }

    /**
     * 실행 취소된 마이그레이션 삭제
     */
    public function delete(string $name): void
    {
        $query = $this->connection
            ->table($this->table)
            ->where('name', '=', $name);

        $result = $query->delete();
    }

    /**
     * 마이그레이션 테이블 삭제
     */
    public function drop(): void
    {
        $this->connection->schema()->dropIfExists($this->table);
    }
}
