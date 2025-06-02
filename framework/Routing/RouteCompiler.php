<?php

namespace Framework\Routing;

use Framework\Routing\Contracts\RouteInterface;

class RouteCompiler
{
    protected RouteInterface $route;

    public function __construct(RouteInterface $route)
    {
        $this->route = $route;
    }

    public function matches(string $uri): bool
    {
        return (bool) preg_match($this->toRegex(), $uri);
    }

    public function extractParameters(string $uri): array
    {
        if (!preg_match($this->toRegex(), $uri, $matches)) {
            return [];
        }

        $parameters = [];

        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $parameters[$key] = $value;
            }
        }

        return $parameters;
    }

    protected function toRegex(): string
    {
        $uri = trim($this->route->uri(), '/');
        $segments = explode('/', $uri);
        $regexParts = [];

        foreach ($segments as $segment) {
            if (preg_match('/^{(\w+)\?}$/', $segment, $matches)) {
                $param = $matches[1];
                $pattern = $this->getPattern($param);
                $regexParts[] = "(?:/(?P<{$param}>{$pattern}))?";
            } elseif (preg_match('/^{(\w+)}$/', $segment, $matches)) {
                $param = $matches[1];
                $pattern = $this->getPattern($param);
                $regexParts[] = "/(?P<{$param}>{$pattern})";
            } else {
                $regexParts[] = '/' . preg_quote($segment, '#');
            }
        }

        $regex = implode('', $regexParts);
        return "#^{$regex}/?$#";
    }

    protected function getPattern(string $param): string
    {
        $patterns = $this->route->getParameterPatterns();
        return $patterns[$param] ?? '[^/]+';
    }
}
