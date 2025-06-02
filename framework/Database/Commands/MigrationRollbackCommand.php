<?php

namespace Framework\Database\Commands;

use Framework\Console\Command;
use Framework\Database\Migration\Migrator;

class MigrationRollbackCommand extends Command
{
    protected string $signature = 'migrate:rollback';
    protected string $description = '마지막 배치의 마이그레이션을 롤백합니다.';
    protected bool $shouldLock = true;

    protected function handle(): void
    {
        /** @var Migrator $migrator */
        $migrator = app()->make(Migrator::class);

        $this->info("🔁 롤백 시작...");

        try {
            $migrator->rollback();
            $this->info("✅ 롤백 완료");
        } catch (\Throwable $e) {
            $this->error("❌ 롤백 실패: " . $e->getMessage());
        }
    }
}
