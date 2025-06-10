<?php

namespace Framework\State;

use Framework\Core\Application;
use Framework\Support\Arr;

class Config
{
    protected ?string $configPath = null;

    protected array $items = []; 

    public function __construct(?string $configPath = null)
    {
        $this->configPath = $configPath ?? Application::getInstance()->configPath(); 
    }

    public function get(string $key, $default = null) 
    {
        [$file, $path] = $this->parseKey($key); 

        if (!isset($this->items[$file])) {
            $this->load($file); 
        }

        return Arr::get($this->items[$file], $path, $default); 
    }

    protected function parseKey(string $key): array
    {
        $parts = explode('.', $key, 2); 
        return [$parts[0], $parts[1] ?? null];
    }

    protected function load(string $file): void
    {
        $path = "{$this->configPath}/{$file}.php"; 

        if (!file_exists($path)) {
            $this->items[$file] = [];
            return; 
        }
        
        $this->items[$file] = require $path; 
    }

    public function all(): array
    {
        return $this->items; 
    }

    public function has(string $key): bool
    {
        [$file, $path] = $this->parseKey($key); 

        if (!isset($this->items[$file])) {
            $this->load($file); 
        }

        return Arr::get($this->items[$file], $path, '__missing__') !== '__missing__';
    }
}
