<?php

namespace Framework\Routing;

use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Http\Contracts\ResponseInterface;
use Framework\Routing\Contracts\RouteInterface;
use Framework\Core\Application;
use Framework\Support\Exceptions\Http\InternalServerErrorException;
use Framework\Support\Exceptions\Http\MethodNotAllowedException;

class ControllerDispatcher
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function dispatch(Request $request, RouteInterface $route): ResponseInterface
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

    protected function invoke(object $controller, string $method, Request $request, RouteInterface $route): ResponseInterface
    {
        if (!method_exists($controller, $method)) {
            throw new MethodNotAllowedException("Method {$method} not found in " . get_class($controller));
        }

        $params = $route->parameters();

        $result = $this->app->call([$controller, $method], $params);

        return $this->normalizeToResponse($result);
    }

    /**
     * 어떠한 반환값도 ResponseInterface로 포장
     */
    protected function normalizeToResponse($result): ResponseInterface
    {
        // 이미 ResponseInterface면 바로 반환
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        // 배열/객체면 JSON 응답
        if (is_array($result) || is_object($result)) {
            return Response::json($result);
        }

        // 문자열이면 HTML 응답
        if (is_string($result)) {
            return Response::html($result);
        }

        // null 등 나머지는 204 No Content
        return Response::html('', 204);
    }
}
