<?php

namespace Framework\Configurations;

use Closure;
use Framework\Support\Exceptions\ExceptionHandler;
use Throwable;

class ExceptionConfigurator 
{
    protected ?ExceptionHandler $handler = null; 

    protected array $ignoreList = []; 

    protected array $reportables = []; 

    protected array $renderables = []; 

    public function use(ExceptionHandler $handler): void
    {
        $this->handler = $handler; 
    }

    public function getHandler(): ?ExceptionHandler
    {
        return $this->handler;
    }

    public function ignore(string $exceptionClass): void
    {
        if (!in_array($exceptionClass, $this->ignoreList, true)) {
            $this->ignoreList[] = $exceptionClass;
        } 
    }

    public function getIgnoreList(): array
    {
        return $this->ignoreList; 
    }

    public function reportable(Closure $callback): void
    {
        $this->reportables[] = $callback;
    }

    public function getReportables(): array
    {
        return $this->reportables;
    }

    public function renderable(Closure $callback): void
    {
        $this->renderables[] = $callback;
    }

    public function getRenderables(): array
    {
        return $this->renderables; 
    }

    public function shouldIgnore(Throwable $e): bool
    {
        foreach ($this->ignoreList as $ignored) {
            if (is_a($e, $ignored)) return true; 
        }

        return false; 
    }
}