<?php

namespace Framework\Filesystem;

use RuntimeException;

class Disk
{
    protected string $root;
    
    /**
     * @var DiskFile[]
     */
    protected array $files = []; 

    protected File $fileHelper; 

    public function __construct(string $root, ?File $file = null)
    {
        $this->root = $root; 

        if (!is_dir($this->root)) {
            throw new RuntimeException("Disk root path does not exist: {$this->root}"); 
        }

        $this->fileHelper = $fileHelper ?? new File(); 
    }

    public function load(): void
    {
        $this->files = []; 

        foreach (scandir($this->root) as $name) {
            if ($name === '.' || $name === '..') {
                continue; 
            }

            $fullPath = $this->fullPath($name); 

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
        $path = $this->fullPath($name); 
        $written = $this->fileHelper->put($path, $contents, $lock); 

        $this->files[$name] = new DiskFile($path, $this->fileHelper); 
        return $written; 
    }

    public function has(string $name): bool
    {
        return isset($this->files[$name]);
    }

    protected function fullPath(string $path): string
    {
        return $this->root . DS . ltrim($path, DS); 
    }

    public function path(string $name = ''): string
    {
        return $this->fullPath($name);
    }
}