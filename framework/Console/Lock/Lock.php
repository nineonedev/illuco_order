<?php

namespace Framework\Console\Lock;

class Lock
{
    protected string $key;
    protected string $filePath;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->filePath = sys_get_temp_dir() . '/console-lock-' . md5($key) . '.lock';
    }

    /**
     * 락을 획득합니다. 이미 락이 존재하면 false를 반환합니다.
     */
    public function acquire(): bool
    {
        if ($this->exists()) {
            return false;
        }

        file_put_contents($this->filePath, (string) getmypid());
        return true;
    }

    /**
     * 락을 해제합니다.
     */
    public function release(): void
    {
        if ($this->exists()) {
            unlink($this->filePath);
        }
    }

    /**
     * 현재 락이 존재하는지 확인합니다.
     */
    public function exists(): bool
    {
        return file_exists($this->filePath);
    }

    /**
     * 락 파일 경로 반환
     */
    public function path(): string
    {
        return $this->filePath;
    }
}
