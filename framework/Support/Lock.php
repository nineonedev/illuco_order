<?php 

namespace Framework\Support; 

class Lock 
{
    protected string $name;
    protected string $directory;

    public function __construct(string $name, string $directory = 'storage/locks')
    {
        $this->name = $name;
        $this->directory = trim($directory, '/');
    }

    public function acquire(): bool
    {
        $this->ensureDirectoryExists();

        if ($this->exists()) {
            return false;
        }

        file_put_contents($this->path(), time());
        return true;
    }

    public function exists(): bool
    {
        return file_exists($this->path());
    }

    public function release(): void
    {
        if ($this->exists()) {
            unlink($this->path());
        }
    }

    public function path(): string
    {
        return $this->directory . '/' . $this->name . '.lock';
    }

    protected function ensureDirectoryExists(): void
    {
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0777, true);
        }
    }
}