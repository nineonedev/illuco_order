<?php

namespace Framework\Filesystem;

use RuntimeException;

class Disk
{
    protected string $root;
    protected ?string $workDir = null; // 서브폴더(작업폴더) 경로

    /** @var DiskFile[] */
    protected array $files = [];

    protected File $fileHelper;

    public function __construct(string $root, ?File $file = null)
    {
        $this->root = $root;

        if (!is_dir($this->root)) {
            throw new RuntimeException("Disk root path does not exist: {$this->root}");
        }

        $this->fileHelper = $file ?? new File();
    }

    /**
     * 작업 디렉토리(컨텍스트) 지정 - 복제본을 반환하므로 체이닝/여러 컨텍스트 OK
     * @param string $subdir 작업할 서브폴더
     * @return static
     */
    public function toWork(string $subdir): self
    {
        $fullPath = $this->fullPath($subdir);

        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        $clone = clone $this;
        $clone->workDir = trim($subdir, DS);

        return $clone;
    }

    public function setRoot(string $newRoot): self
    {
        if (!is_dir($newRoot)) {
            throw new RuntimeException("Invalid disk root path: {$newRoot}");
            // error_log("Warning: Attempted to set invalid disk root path: {$newRoot}");
            // return $this; 
        }

        $this->root = $newRoot;
        $this->workDir = null;
        $this->files = [];

        return $this;
    }

    /**
     * 실제 파일이 저장될 경로(디렉토리)
     * @param string $name 파일명 (없으면 디렉토리 자체)
     * @return string
     */
    public function path(string $name = ''): string
    {
        // workDir가 있다면 workDir 기준, 없으면 root 기준
        $base = $this->workDir
            ? rtrim($this->root, DS) . DS . $this->workDir
            : $this->root;

        return $name
            ? rtrim($base, DS) . DS . ltrim($name, DS)
            : rtrim($base, DS);
    }

    public function load(): void
    {
        $dir = $this->workDir ? $this->path() : $this->root;
        $this->files = [];

        foreach (scandir($dir) as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }

            $fullPath = $this->path($name);

            if (is_file($fullPath)) {
                $this->files[$name] = new DiskFile($fullPath, $this->fileHelper);
            }
        }
    }

    public function names(): array
    {
        return array_keys($this->files);
    }

    public function files(): array
    {
        return $this->files;
    }

    public function get(string $name): DiskFile
    {
        if (!$this->has($name)) {
            throw new RuntimeException("File not found on disk: {$name}");
        }

        return $this->files[$name];
    }

    public function delete(string $name): bool
    {
        if (!$this->has($name)) {
            return false;
        }

        $deleted = $this->files[$name]->delete();

        if ($deleted) {
            unset($this->files[$name]);
        }

        return $deleted;
    }

    public function put(string $name, string $contents, bool $lock = false): int
    {
        $path = $this->path($name);
        $written = $this->fileHelper->put($path, $contents, $lock);

        $this->files[$name] = new DiskFile($path, $this->fileHelper);
        return $written;
    }

    public function has(string $name): bool
    {
        return isset($this->files[$name]);
    }

    /**
     * 내부용: root 또는 workDir를 기반으로 전체 경로 반환
     */
    protected function fullPath(string $path): string
    {
        return rtrim($this->root, DS) . DS . ltrim($path, DS);
    }
}
