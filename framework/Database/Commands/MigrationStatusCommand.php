<?php

namespace Framework\Database\Commands;

use Framework\Console\Command;
use Framework\Database\Migration\MigrationRepository;

class MigrationStatusCommand extends Command
{
    protected string $signature = 'migrate:status';
    protected string $description = '실행된 마이그레이션 목록을 확인합니다.';
    protected bool $shouldLock = false;

    protected function handle(): void
    {
        /** @var MigrationRepository $repository */
        $repository = app()->make(MigrationRepository::class);

        $ran = $repository->getRan();

        if (empty($ran)) {
            $this->info("📭 실행된 마이그레이션이 없습니다.");
            return;
        }

        $this->info("✅ 실행된 마이그레이션 목록:");
        foreach ($ran as $name) {
            $this->line("  └ {$name}");
        }
    }
}
