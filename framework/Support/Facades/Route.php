<?php

namespace Framework\Support\Facades;

use Framework\Routing\Route as RouteInstance;
use Framework\Routing\RouteCollection;
use Framework\Routing\RouteRegistrar;
use Framework\Routing\Contracts\RouteInterface;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Routing\Contracts\RouterInterface;
use Framework\Routing\RouteGroupRegistrar;

/**
 * @method static RouteInstance get(string $uri, $action)
 * @method static RouteInstance post(string $uri, $action)
 * @method static RouteInstance put(string $uri, $action)
 * @method static RouteInstance patch(string $uri, $action)
 * @method static RouteInstance delete(string $uri, $action)
 * @method static RouteInstance any(string $uri, $action)
 * @method static RouteInstance view(string $uri, string $template, array $data = [])
 * @method static RouteGroupRegistrar middleware(string|array $middleware)
 * @method static RouteGroupRegistrar prefix(string $prefix)
 * @method static RouteGroupRegistrar name(string $name)
 * @method static void group(\Closure $callback)
 * @method static string route(string $name, array $params = [])
 * @method static string currentUrl()
 * @method static bool currentUrlContains(string $path)
 * @method static bool currentUrlStartsWith(string $path)
 * @method static RouteCollection getRoutes()
 * @method static RouteRegistrar registrar()
 * @method static ?RouteInterface match(Request $request)
 * @method static Response dispatch(Request $request)
 */
class Route extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RouterInterface::class; 
    }
}
