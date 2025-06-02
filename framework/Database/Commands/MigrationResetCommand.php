<?php

namespace Framework\Database\Commands;

use Framework\Console\Command;
use Framework\Database\Migration\MigrationRepository;

class MigrationResetCommand extends Command
{
    protected string $signature = 'migrate:reset';
    protected string $description = '모든 마이그레이션 실행 기록을 초기화합니다.';
    protected bool $shouldLock = true;

    protected function handle(): void
    {
        /** @var MigrationRepository $repository */
        $repository = app()->make(MigrationRepository::class);

        $this->warn("⚠ 마이그레이션 실행 기록이 모두 삭제됩니다.");
        $repository->drop();

        $this->info("✅ 마이그레이션 테이블이 삭제되었습니다.");
    }
}
