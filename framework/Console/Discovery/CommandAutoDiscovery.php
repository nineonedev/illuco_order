<?php

namespace Framework\Console\Discovery;

use Framework\Console\CommandRegistry;
use Framework\Console\Contracts\CommandInterface;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use ReflectionClass;

class CommandAutoDiscovery
{
    protected string $baseNamespace;
    protected string $basePath;

    public function __construct(string $baseNamespace, string $basePath)
    {
        $this->baseNamespace = rtrim($baseNamespace, '\\');
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * 지정된 디렉토리 내에서 Command 클래스들을 자동 검색하여 등록합니다.
     */
    public function discoverInto(CommandRegistry $registry): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->basePath)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            // 파일 경로를 클래스 이름으로 변환
            $relativePath = str_replace([$this->basePath . '/', '.php'], '', $file->getPathname());
            $relativeClass = str_replace('/', '\\', $relativePath);
            $class = $this->baseNamespace . '\\' . $relativeClass;

            // 존재하고 CommandInterface를 구현하는 경우에만 등록
            if (!class_exists($class)) {
                continue;
            }

            $ref = new ReflectionClass($class);

            if (
                $ref->isInstantiable() &&
                $ref->implementsInterface(CommandInterface::class)
            ) {
                $registry->add($ref->newInstance());
            }
        }
    }
}
