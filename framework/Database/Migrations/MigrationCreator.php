<?php

namespace Framework\Database\Migrations;

class MigrationCreator
{
    protected string $path;

    public function __construct(string $path = 'database/migrations')
    {
        $this->path = $path;
    }

    /**
     * 새로운 마이그레이션 파일 생성
     */
    public function create(string $name): string
    {
        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_{$name}.php";
        $filePath = "{$this->path}/{$fileName}";

        $stub = $this->getStub($name);

        file_put_contents($filePath, $stub);

        return $filePath;
    }

    /**
     * 마이그레이션 파일의 기본 내용 반환
     */
    protected function getStub(string $className): string
    {
        $class = $this->generateClassName($className);

        return <<<PHP
<?php

use Framework\Database\Contracts\Migration;

class {$class} implements Migration
{
    public function up(): void
    {
        // TODO: Implement migration logic
    }

    public function down(): void
    {
        // TODO: Implement rollback logic
    }
}

PHP;
    }

    /**
     * 클래스명으로 사용할 이름 생성
     */
    protected function generateClassName(string $name): string
    {
        $name = str_replace(['-', '_'], ' ', $name);
        $name = ucwords($name);
        return str_replace(' ', '', $name);
    }
}
