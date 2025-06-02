<?php

namespace Framework\Routing\Contracts;

use Closure;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Routing\Route;
use Framework\Routing\RouteCollection;
use Framework\Routing\RouteGroupRegistrar;

interface RouterInterface
{
    public function get(string $uri, $action): Route;

    public function post(string $uri, $action): Route;

    public function put(string $uri, $action): Route;

    public function patch(string $uri, $action): Route;

    public function delete(string $uri, $action): Route;

    public function any(string $uri, $action): Route;

    public function prefix(string $prefix): RouteGroupRegistrar;

    public function name(string $name): RouteGroupRegistrar;

    /**
     * @param string|array<int, string> $middleware
     */
    public function middleware($middleware): RouteGroupRegistrar;

    public function group(Closure $callback): void;

    public function route(string $name, array $params = []): string;

    public function currentUrl(): string;

    public function currentUrlContains(string $path): bool;

    public function currentUrlStartsWith(string $path): bool;

    public function dispatch(Request $request): Response;

    public function match(Request $request): ?RouteInterface;

    public function getRoutes(): RouteCollection;
}
