<?php

namespace Framework\View;

use Framework\View\Contracts\ViewFinderInterface;
use Framework\View\Exceptions\ViewNotFoundException;

class ViewFinder implements ViewFinderInterface
{
    protected string $basePath;

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, DS);
    }

    public function find(string $view): string
    {
        $relativePath = str_replace('.', DS, $view) . '.php';
        $fullPath = $this->basePath . DS . $relativePath;

        if (!file_exists($fullPath)) {
            throw new ViewNotFoundException("View [{$view}] not found at path: {$fullPath}");
        }

        return $fullPath;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }
}