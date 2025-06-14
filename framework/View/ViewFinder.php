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

    protected function buildFullPath(string $view): string
    {
        $basePath = rtrim($this->basePath, '/');

        // 이미 basePath가 view에 포함되어 있으면 잘라냄
        if (str_starts_with($view, $basePath)) {
            $view = ltrim(substr($view, strlen($basePath)), '/\\');
        }

        // .php 확장자가 붙어 있으면 제거
        if (str_ends_with($view, '.php')) {
            $view = substr($view, 0, -4);
        }

        // 점 표기 → 디렉토리 변환
        $relativePath = str_replace('.', '/', $view) . '.php';

        return $basePath . '/' . $relativePath;
    }
}
