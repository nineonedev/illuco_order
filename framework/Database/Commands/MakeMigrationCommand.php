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
        $this->addOption('table');
    }

    protected function handle(): void
    {
        $name  = trim((string) $this->argument('name'));
        $table = trim((string) $this->option('table'));

        if ($name === '') {
            throw new InvalidArgumentException(
                "마이그레이션 이름을 지정해야 합니다.\n예: php console make:migration create_users_table"
            );
        }

        $isCreate = preg_match('/^create_.*_table$/', $name);
        $isAlter  = preg_match('/^(add|drop|rename|modify)_.*_to_.*_table$/', $name);

        if (!$table && preg_match('/(?:create|add|drop|rename|modify)_(.*?)_table/', $name, $m)) {
            $table = $m[1];
        }

        $stubFile = $isCreate ? 'migration.create' : 'migration.table';

        $filename   = date('Ymd_His') . '_' . $this->snake($name);
        $className  = $this->classify($name);
        $targetPath = base_path("database/migrations/{$filename}.php");
        $stubPath   = base_path('framework/Database/stubs');

        $stub = new Stub($stubPath);

        try {
            $stub->generate($stubFile, $targetPath, [
                'class' => $className,
                'table' => $table,
            ]);
        } catch (\RuntimeException $e) {
            $this->error("❌ 마이그레이션 생성 실패: {$e->getMessage()}");
            $this->error("└ 생성 시도된 파일: {$filename}.php");
            return;
        }

        $this->info("✔ 마이그레이션 생성 완료: {$filename} ({$targetPath})");
    }

    protected function snake(string $value): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace(' ', '_', $value)));
    }

    protected function classify(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }
}
