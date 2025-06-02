<?php

namespace Framework\Database\Commands;

use Framework\Console\Command;
use Framework\Database\Migration\Migrator;

class MigrationFreshCommand extends Command
{
    protected string $signature = 'migrate:fresh';
    protected string $description = '모든 마이그레이션을 초기화하고 다시 실행합니다.';
    protected bool $shouldLock = true;

    protected function handle(): void
    {
        /** @var Migrator $migrator */
        $migrator = app()->make(Migrator::class);

        $this->warn("⚠ 모든 마이그레이션이 삭제되고 다시 실행됩니다.");
        $this->line("───────────────────────────────");

        try {
            $migrator->fresh();
            $this->info("✅ 마이그레이션 초기화 완료");
        } catch (\Throwable $e) {
            $this->error("❌ 초기화 실패: " . $e->getMessage());
        }
    }
}
