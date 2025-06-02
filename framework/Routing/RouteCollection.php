<?php

namespace Framework\Routing;

use Framework\Routing\Contracts\RouteInterface;

class RouteCollection
{
    /**
     * @var array<string, RouteInterface[]>
     */
    protected array $routes = [];

    /**
     * @var array<string, RouteInterface>
     */
    protected array $namedRoutes = [];

    public function add(RouteInterface $route): void
    {
        $method = strtoupper($route->method());

        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        $this->routes[$method][] = $route;

        if ($route->getName()) {
            $this->namedRoutes[$route->getName()] = $route;
        }
    }

    public function match(string $method, string $uri): ?RouteInterface
    {
        $method = strtoupper($method);
        $uri = rtrim($uri, '/') ?: '/';

        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $route) {
            if ($route->matches($method, $uri)) {
                return $route;
            }
        }

        return null;
    }

    public function getByName(string $name): ?RouteInterface
    {
        return $this->namedRoutes[$name] ?? null;
    }

    public function all(): array
    {
        return $this->routes;
    }

    public function allNamed(): array
    {
        return $this->namedRoutes;
    }
}
