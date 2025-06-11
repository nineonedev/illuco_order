<?php

namespace Framework\Routing;

use Closure;
use Framework\Http\Request;
use Framework\Http\Contracts\ResponseInterface;
use Framework\Routing\Contracts\RouteInterface;
use Framework\Routing\Contracts\RouterInterface;

class Router implements RouterInterface
{
    protected RouteCollection $routes;
    protected ControllerDispatcher $dispatcher;
    protected RouteRegistrar $registrar;
    protected RouteGroupRegistrar $groupRegistrar; 
    protected UrlGenerator $urlGenerator;

    public function __construct(
        ?ControllerDispatcher $dispatcher = null,
        ?UrlGenerator $urlGenerator = null
    ) {
        $this->routes = new RouteCollection();
        $this->dispatcher = $dispatcher ?? app()->make(ControllerDispatcher::class);
        $this->registrar = new RouteRegistrar($this->routes);
        $this->groupRegistrar = new RouteGroupRegistrar($this->registrar);
        $this->urlGenerator = $urlGenerator ?? new UrlGenerator($this->routes);
    }

    /**
     * 등록된 모든 라우트 컬렉션 반환
     */
    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }

    /**
     * 라우트 등록 전담 클래스 반환
     */
    public function registrar(): RouteRegistrar
    {
        return $this->registrar;
    }

    public function view(string $uri, string $template, array $data = []): Route
    {
        return $this->get($uri, function() use($template, $data){
            return view($template, $data); 
        }); 
    }

    /**
     * HTTP 요청에 맞는 라우트 매칭 시도
     */
    public function match(Request $request): ?RouteInterface
    {
        $route = $this->routes->match($request->method(), $request->path());

        if ($route) {
            $route->resolveParametersFromPath($request->path());
        }

        return $route;
    }

    /**
     * 요청 처리 및 컨트롤러 실행
     */
    public function dispatch(Request $request): ResponseInterface
    {
        return $this->dispatcher->dispatch($request, $request->route());
    }

    public function name(string $name): RouteGroupRegistrar
    {
        return $this->groupRegistrar->name($name); 
    }

    public function prefix(string $prefix): RouteGroupRegistrar
    {
        return $this->groupRegistrar->prefix($prefix); 
    }

    /**
     * @param string|array $middleware
     */
    public function middleware($middleware): RouteGroupRegistrar
    {
        return $this->groupRegistrar->middleware($middleware);
    }

    public function group(Closure $callback): void
    {
        $this->groupRegistrar->group($callback);
    }

    public function groupRegistrar(): RouteGroupRegistrar
    {
        return $this->groupRegistrar; 
    }

    public function urlFor(string $name, array $params = []): string
    {
        return $this->urlGenerator->route($name, $params); 
    }

    /**
     * GET 라우트 등록 위임
     */
    public function get(string $uri, $action): Route
    {
        return $this->registrar()->get($uri, $action);
    }

    /**
     * POST 라우트 등록 위임
     */
    public function post(string $uri, $action): Route
    {
        return $this->registrar()->post($uri, $action);
    }

    /**
     * PUT 라우트 등록 위임
     */
    public function put(string $uri, $action): Route
    {
        return $this->registrar()->put($uri, $action);
    }

    /**
     * PATCH 라우트 등록 위임
     */
    public function patch(string $uri, $action): Route
    {
        return $this->registrar()->patch($uri, $action);
    }

    /**
     * DELETE 라우트 등록 위임
     */
    public function delete(string $uri, $action): Route
    {
        return $this->registrar()->delete($uri, $action);
    }

    /**
     * ANY 라우트 등록 위임
     */
    public function any(string $uri, $action): Route
    {
        return $this->registrar()->any($uri, $action);
    }

    /**
     * 네임드 라우트에 기반한 URL 생성
     */
    public function route(string $name, array $params = []): string
    {
        return $this->urlGenerator->route($name, $params);
    }

    /**
     * 현재 요청 URL을 스킴 포함하여 반환
     */
    public function currentUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return $scheme . '://' . $host . $uri;
    }

    /**
     * 현재 URL에 특정 문자열 포함 여부 확인
     */
    public function currentUrlContains(string $path): bool
    {
        return strpos($this->currentUrl(), $path) !== false;
    }

    /**
     * 현재 URL이 특정 문자열로 시작하는지 여부 확인
     */
    public function currentUrlStartsWith(string $path): bool
    {
        return strpos($this->currentUrl(), $path) === 0;
    }
}
