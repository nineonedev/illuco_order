<?php

namespace Framework\Translation;

use Framework\Translation\Contracts\LoaderInterface;

class FileLoader implements LoaderInterface
{
    protected string $path; 

    /**
     * @var array<string,array>
     */
    protected array $loaded = [];

    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/'); 
    }

    public function load(string $locale): array
    {
        if (isset($this->loaded[$locale])) {
            return $this->loaded[$locale]; 
        }

        $file = "{$this->path}/{$locale}.php"; 

        if (!file_exists($file)) {
            return [];
        }

        $this->loaded[$locale] = require $file; 

        return $this->loaded[$locale]; 
    }

    public function getPath(): string
    {
        return $this->path; 
    }

    public function setPath(string $path): void
    {
        $this->path = $path; 
    }
}