<?php

namespace Framework\View;

use Framework\Core\Application;
use Framework\View\Contracts\ViewFinderInterface;
use Framework\View\Exceptions\ViewNotFoundException;

class ViewFinder implements ViewFinderInterface
{
    protected ?string $basePath = null;

    public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?? Application::getInstance()->viewPath();
    }

    public function find(string $view): string
    {
        $fullPath = $this->buildFullPath($view);

        if (!file_exists($fullPath)) {
            throw new ViewNotFoundException("View [{$view}] not found at path: {$fullPath}");
        }

        return $fullPath;
    }

    public function exists(string $view): bool
    {
        return file_exists($this->buildFullPath($view));
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * 뷰 이름을 전체 파일 경로로 변환
     */
    protected function buildFullPath(string $view): string
    {
        $relativePath = str_replace('.', '/', $view) . '.php';
        return rtrim($this->basePath, '/') . '/' . $relativePath;
    }
}
