<?php

namespace Framework\Support;

class Stub
{
    protected string $stubPath;

    public function __construct(string $stubPath)
    {
        $this->stubPath = rtrim($stubPath, '/');
    }

    public function getPath(string $filename): string
    {
        return "{$this->stubPath}/{$filename}.stub";
    }

    public function render(string $filename, array $replacements = []): string
    {
        $path = $this->getPath($filename);

        if (!file_exists($path)) {
            throw new \RuntimeException("Stub file [{$path}] not found.");
        }

        $content = file_get_contents($path);

        foreach ($replacements as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
    }

    public function generate(string $filename, string $destination, array $replacements = []): void
    {
        $content = $this->render($filename, $replacements);

        $dir = dirname($destination);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($destination, $content);
    }
}
