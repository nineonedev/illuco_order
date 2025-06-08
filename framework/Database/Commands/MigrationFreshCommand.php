<?php

namespace Framework\Database\Commands;

use Framework\Console\Command;
use Framework\Console\UI\ProgressBar;
use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Migration\Migrator;
use Framework\Database\Schema\Schema;

class MigrationFreshCommand extends Command
{
    protected string $signature = 'migrate:fresh';
    protected string $description = '모든 마이그레이션을 초기화하고 다시 실행합니다.';
    protected bool $shouldLock = true;

    protected function handle(): void
    {
        /** @var ConnectionInterface $db */
        $db = app(ConnectionInterface::class);

        $this->warn("⚠ 모든 테이블을 삭제합니다. (데이터 손실!)");
        $this->line("───────────────────────────────");

        // 모든 테이블 DROP
        $tables = $db->schema()->getAllTables();
        $bar = new ProgressBar(count($tables), 'Dropping Tables', 'done'); 
        
        try {
            $db->schema()->freshAllTables(function(Schema $_, string $table) use ($bar) {
                $this->line("Dropped: $table");
                $bar->advance();
            });
            
            $bar->finish();
            $this->info("✅ 마이그레이션 초기화 완료");

        } catch (\Throwable $e) {
            $this->error("❌ 초기화 실패: " . $e->getMessage());
        }
    }
}
