<?php

namespace Framework\Routing;

use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Routing\Contracts\RouteInterface;
use Framework\Core\Application;
use Framework\Support\Exceptions\Http\InternalServerErrorException;
use Framework\Support\Exceptions\Http\MethodNotAllowedException;
use Throwable;

class ControllerDispatcher
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function dispatch(Request $request, RouteInterface $route): Response
    {
        $action = $route->action();

       // [ControllerClass::class, 'method']
        if (is_array($action) && count($action) === 2) {
            [$class, $method] = $action;

            $controller = $this->app->make($class);
            return $this->invoke($controller, $method, $request, $route);
        }

        // "Controller@method"
        if (is_string($action) && strpos($action, '@') !== false) {
            [$class, $method] = explode('@', $action);
            $controller = $this->app->make($class);
            return $this->invoke($controller, $method, $request, $route);
        }

        // Closure or callable
        if (is_callable($action)) {
            return $this->normalizeToResponse(
                $this->app->call($action, $route->parameters())
            );
        }

        throw new InternalServerErrorException("Invalid route action.");
    }

    protected function invoke(object $controller, string $method, Request $request, RouteInterface $route): Response
    {
        if (!method_exists($controller, $method)) {
            throw new MethodNotAllowedException("Method {$method} not found in " . get_class($controller));
        }

        $params = $route->parameters();

        $result = $this->app->call([$controller, $method], $params);

        return $this->normalizeToResponse($result);
    }

    protected function normalizeToResponse($result): Response
    {
        if ($result instanceof Response) {
            return $result;
        }

        if (is_array($result)) {
            return Response::json($result);
        }

        return new Response((string) $result); 
    }
}
