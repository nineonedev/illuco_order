<?php

namespace Framework\Routing;

use Framework\Routing\Contracts\RouteInterface;
use Framework\Http\Contracts\MiddlewareInterface;

class Route implements RouteInterface
{
    protected string $method;
    protected string $uri;
    protected $action;

    protected ?string $name = null;
    protected ?string $namePrefix = null; 

    protected array $middleware = [];
    protected array $middlewareGroups = [];

    protected array $parameterPatterns = [];
    protected array $parameters = [];

    protected ?RouteCollection $routes = null;

    public function __construct(string $method, string $uri, $action)
    {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->action = $action;
    }

    public function setRouteCollection(RouteCollection $routes): void
    {
        $this->routes = $routes;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function action()
    {
        return $this->action;
    }

    public function setNamePrefix(string $prefix): void
    {
        $this->namePrefix = rtrim($prefix, '.') . '.';
    }

    public function name(string $name): RouteInterface
    {
        $fullName = $this->namePrefix ? $this->namePrefix . $name : $name;
        
        if ($this->name !== null) return $this;

        $this->name = $fullName;

        if ($this->routes) {
            $this->routes->addNamedRoute($this);
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function middleware($middleware): RouteInterface
    {
        $middlewares = is_array($middleware) ? $middleware : [$middleware];

        foreach ($middlewares as $item) {
            if (is_string($item) && class_exists($item) && is_subclass_of($item, MiddlewareInterface::class)) {
                $this->middleware[] = $item;
            } elseif (is_string($item)) {
                $this->middlewareGroups[] = $item;
            }
        }

        $this->middleware = array_unique($this->middleware);
        $this->middlewareGroups = array_unique($this->middlewareGroups);
        
        return $this;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function getMiddlewareGroups(): array
    {
        return $this->middlewareGroups;
    }

    public function matches(string $method, string $uri): bool
    {
        if (strtoupper($method) !== $this->method) {
            return false;
        }

        return (new RouteCompiler($this))->matches($uri);
    }

    public function resolveParametersFromPath(string $path): void
    {
        $this->parameters = (new RouteCompiler($this))->extractParameters($path);
    }

    public function parameters(): array
    {
        return $this->parameters;
    }

    public function parameter(string $key, $default = null)
    {
        return $this->parameters[$key] ?? $default;
    }

    public function where(string $param, string $pattern): RouteInterface
    {
        $this->parameterPatterns[$param] = $pattern;
        return $this;
    }

    public function whereNumber(string $param): RouteInterface
    {
        return $this->where($param, '\d+');
    }

    public function whereAlpha(string $param): RouteInterface
    {
        return $this->where($param, '[a-zA-Z]+');
    }

    public function whereAlphaNumeric(string $param): RouteInterface
    {
        return $this->where($param, '[a-zA-Z0-9]+');
    }

    public function whereUuid(string $param): RouteInterface
    {
        return $this->where($param, '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}');
    }

    public function whereUlid(string $param): RouteInterface
    {
        return $this->where($param, '[0-9a-zA-Z]{26}');
    }

    public function whereIn(string $param, array $values): RouteInterface
    {
        $escaped = array_map('preg_quote', $values);
        $pattern = implode('|', $escaped);
        return $this->where($param, $pattern);
    }

    public function getParameterPatterns(): array
    {
        return $this->parameterPatterns;
    }
}
