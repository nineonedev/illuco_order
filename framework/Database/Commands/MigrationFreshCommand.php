<?php

namespace Framework\Database\Commands;

use Framework\Console\Command;
use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Migration\Migrator;

class MigrationFreshCommand extends Command
{
    protected string $signature = 'migrate:fresh';
    protected string $description = '모든 마이그레이션을 초기화하고 다시 실행합니다.';
    protected bool $shouldLock = true;

    protected function handle(): void
    {
        /** @var ConnectionInterface $db */
        $migrator = app(Migrator::class);

        $this->warn("⚠ 모든 테이블을 삭제합니다. (데이터 손실!)");
        $this->line("───────────────────────────────");

        try {
            $migrator->fresh();
            $this->info("✅ 마이그레이션 초기화 및 실행 완료");
        } catch (\Throwable $e) {
            $this->error("❌ 마이그레이션 실패: {$e->getMessage()}");
        }
    }
}
