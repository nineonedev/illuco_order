<?php

namespace Framework\Http;

use Framework\Support\Exceptions\Http\NotFoundException;
use Framework\Core\Application;
use Framework\Routing\Router;
use Framework\Routing\Route;
use Framework\Http\Response;
use Framework\Http\Pipeline;
use Framework\Core\Contracts\KernelInterface;
use Framework\Configurations\MiddlewareConfigurator;

class Kernel implements KernelInterface
{
    protected Application $app;
    protected Router $router;
    protected MiddlewareConfigurator $middleware;

    public function __construct(
        Application $app,
        Router $router,
        MiddlewareConfigurator $middleware
    ) {
        $this->app = $app;
        $this->router = $router;
        $this->middleware = $middleware;
    }

    public function handle($request): Response
    {
        // 현재 요청을 컨테이너에 바인딩
        $this->app->swap(Request::class, $request);

        $route = $this->router->match($request);

        if (!$route) {
            throw new NotFoundException("404 Not Found");
        }

        $request->setRoute($route);

        // 미들웨어 수집 및 처리
        $middlewares = $this->gatherMiddlewares($route);

        return (new Pipeline($this->app))
            ->send($request)
            ->through($middlewares)
            ->then(function ($request) {
                return $this->router->dispatch($request);
            });
    }

    public function terminate($request, $response = null): void
    {
        $route = $request->route();

        $middlewares = $this->gatherMiddlewares($route);

        foreach ($middlewares as $middleware) {
            if (is_string($middleware)) {
                $middleware = $this->app->make($middleware);
            }

            if (method_exists($middleware, 'terminate')) {
                $middleware->terminate($request, $response);
            }
        }

        // 추후: session 저장, 로그, 큐 flush 등 추가 가능
    }

    /**
     * 미들웨어 수집 (global, group, route)
     */
    protected function gatherMiddlewares(?Route $route): array
    {
        if (!$route) return $this->middleware->sync(); 

        return $this->middleware->sync(
            $route->getMiddlewareGroups(),
            $route->getMiddleware()
        );
    }
}
