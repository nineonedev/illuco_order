<?php

namespace Framework\Configurations;

use Framework\Http\Middleware\MiddlewareInterface;

class MiddlewareConfigurator
{
    /**
     * @var array<class-string<MiddlewareInterface>>
     */
    protected array $global = [];

    /**
     * @var array<class-string<MiddlewareInterface>>
     */
    protected array $priority = [];

    /**
     * @var array<string,array<class-string<MiddlewareInterface>>>
     */
    protected array $groups = [];

    /**
     * @param array<class-string<MiddlewareInterface>> $middlewares
     */
    public function addGlobalMany(array $middlewares = []): void
    {
        foreach ($middlewares as $middleware) {
            $this->addGlobal($middleware); 
        }
    }

    /**
     * @param class-string<MiddlewareInterface> $middleware
     */
    public function addGlobal(string $middleware): void
    {
        if (!in_array($middleware, $this->global, true)) {
            $this->global[] = $middleware;
        }
    }

    /**
     * @param array<class-string<MiddlewareInterface>> $middlewares
     */
    public function addGroupMany(string $group, array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            $this->addGroup($group, $middleware);
        }
    }

    public function addGroup(string $group, string $middleware): void
    {
        if (!isset($this->groups[$group])) {
            $this->groups[$group] = [];
        }

        if (!in_array($middleware, $this->groups[$group], true)) {
            $this->groups[$group][] = $middleware;
        }
    }

    /**
     * @param class-string<MiddlewareInterface> $middleware
     */
    public function addPriority(string $middleware): void
    {
        if (!in_array($middleware, $this->priority, true)) {
            $this->priority[] = $middleware;
        }
    }

    /**
     * @param array<class-string<MiddlewareInterface>> $middlewares
     */
    public function addPriorityMany(array $middlewares = []): void
    {
        foreach ($middlewares as $middleware) {
            $this->addPriority($middleware); 
        }
    }

    public function getGlobal(): array
    {
        return $this->global;
    }

    public function getPriority(): array
    {
        return $this->priority;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function getGroup(string $group): array
    {
        return $this->groups[$group] ?? [];
    }

    /**
     * Sync middleware in order: priority > global > groups > route-specific
     *
     * @param array $groups
     * @param array $routeMiddlewares
     * @return array<class-string<MiddlewareInterface>>
     */
    public function sync(array $groups = [], array $routeMiddlewares = []): array
    {
        $middlewares = [];

        // 1. Add global
        $middlewares = array_merge($middlewares, $this->global);

        // 2. Add group
        foreach ($groups as $group) {
            if (isset($this->groups[$group])) {
                $middlewares = array_merge($middlewares, $this->groups[$group]);
            }
        }

        // 3. Add route-specific
        $middlewares = array_merge($middlewares, $routeMiddlewares);

        // Remove duplicates
        $middlewares = array_unique($middlewares);

        // 4. Sort by priority (if applicable)
        if (!empty($this->priority)) {
            dump($this->priority); 


            usort($middlewares, function ($a, $b) {
                $priorityA = array_search($a, $this->priority, true);
                $priorityB = array_search($b, $this->priority, true);

                $priorityA = $priorityA === false ? PHP_INT_MAX : $priorityA;
                $priorityB = $priorityB === false ? PHP_INT_MAX : $priorityB;

                return $priorityA <=> $priorityB;
            });
        }

        return $middlewares;
    }
}
