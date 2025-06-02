<?php

namespace Framework\Database\Commands;

use Framework\Console\Command;
use Framework\Support\Stub;
use InvalidArgumentException;

class MakeMigrationCommand extends Command
{
    protected string $signature = 'make:migration';
    protected string $description = '새로운 마이그레이션 파일을 생성합니다.';
    protected bool $shouldLock = false;

    protected function configure(): void
    {
        $this->addArgument('name');
        $this->addOption('table'); // --table 옵션 추가
    }

    protected function handle(): void
    {
        $name = trim((string) $this->argument('name'));
        $table = trim((string) $this->option('table'));

        if ($name === '') {
            throw new InvalidArgumentException(
                "마이그레이션 이름을 지정해야 합니다.\n예: php console make:migration create_users_table"
            );
        }

        if ($table === '') {
            throw new InvalidArgumentException('--table 옵션은 필수입니다.');
        }

        $filename   = date('Ymd_His') . '_' . $this->snake($name);
        $className  = $this->classify($name);
        $stubFile   = 'migration';
        $targetPath = base_path("database/migrations/{$filename}.php");
        $stubPath   = base_path('framework/Database/stubs');

        $stub = new Stub($stubPath);

        try {
            $stub->generate($stubFile, $targetPath, [
                'class' => $className,
                'table' => $table,
            ]);
        } catch (\RuntimeException $e) {
            $this->error("❌ 마이그레이션 생성 실패: " . $e->getMessage());
            return;
        }

        $this->info("✔ 마이그레이션 생성 완료: {$filename} ({$targetPath})");
    }
}
