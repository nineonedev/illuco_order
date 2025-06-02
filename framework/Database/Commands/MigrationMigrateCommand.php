<?php

namespace Framework\Database\Commands;

use Framework\Console\Command;
use Framework\Database\Migration\Migrator;
use InvalidArgumentException;

class MigrationMigrateCommand extends Command
{
    protected string $signature = 'migrate';
    protected string $description = '모든 마이그레이션을 실행합니다.';
    protected bool $shouldLock = true;

    protected function configure(): void
    {
        // 옵션 정의 예시 (향후 확장 가능)
        $this->addOption('path', base_path('database/migrations'));
    }

    protected function handle(): void
    {
        /** @var Migrator $migrator */
        $migrator = app()->make(Migrator::class);

        $path = $this->option('path');

        if (!is_dir($path)) {
            $this->error("❌ 마이그레이션 경로가 존재하지 않습니다: {$path}");
            return;
        }

        $this->info("📂 마이그레이션 실행 중... [경로: {$path}]");

        try {
            $migrator->migrate();
            $this->info("✅ 마이그레이션 완료");
        } catch (\Throwable $e) {
            $this->error("❌ 마이그레이션 실패: " . $e->getMessage());
        }
    }
}
