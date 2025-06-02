<?php

namespace Framework\Routing;

use Framework\Routing\Contracts\RouteInterface;

class Route implements RouteInterface
{
    protected string $method;
    protected string $uri;
    protected $action;

    protected ?string $name = null;
    protected array $middleware = [];
    protected array $middlewareGroups = [];

    protected array $parameterPatterns = [];
    protected array $parameters = [];

    public function __construct(string $method, string $uri, $action)
    {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->action = $action;
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

    public function name(string $name): RouteInterface
    {
        if ($this->name !== null) return $this;
        
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function middleware($middleware): RouteInterface
    {
        if (is_string($middleware)) {
            $this->middleware[] = $middleware;
        } elseif (is_array($middleware)) {
            $this->middleware = array_merge($this->middleware, $middleware);
        }

        $this->middleware = array_unique($this->middleware);
        return $this;
    }

    /**
     * 미들웨어 그룹 설정 (단일)
     */
    public function middlewareGroup(string $group): RouteInterface
    {
        $this->middlewareGroups[] = $group;
        $this->middlewareGroups = array_unique($this->middlewareGroups);
        return $this;
    }

    /**
     * 미들웨어 그룹 설정 (다중)
     */
    public function middlewareGroups(array $groups): RouteInterface
    {
        $this->middlewareGroups = array_merge($this->middlewareGroups, $groups);
        $this->middlewareGroups = array_unique($this->middlewareGroups);
        return $this;
    }

    /**
     * 설정된 미들웨어 그룹 반환
     */
    public function getMiddlewareGroups(): array
    {
        return $this->middlewareGroups;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
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
