<?php

use Framework\Http\Responses\RedirectResponse;
use Framework\Routing\Contracts\RouterInterface;
use Framework\Routing\RouteCollection;
use Framework\Support\Facades\Route;

if (!function_exists('route')) {
    /**
     * 네임드 라우트 기반 URL 생성
     *
     * @param string $name
     * @param array $params
     * @return string
     */
    function route(string $name, array $params = []): string
    {
        return Route::route($name, $params);
    }
}

if (!function_exists('redirect_route')) {
    /**
     * 라우트 이름 기반으로 리다이렉트(302) 응답 반환
     *
     * @param string $name
     * @param array $params
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    function redirect_route(string $name, array $params = [], int $status = 302, array $headers = [])
    {
        $url = route($name, $params);
        return redirect($url, $status, $headers);
    }
}


if (!function_exists('current_url')) {
    /**
     * 현재 요청 URL 반환
     *
     * @return string
     */
    function current_url(): string
    {
        return Route::currentUrl();
    }
}

if (!function_exists('url_has')) {
    /**
     * 현재 URL이 특정 문자열을 포함하는지 확인
     *
     * @param string $path
     * @return bool
     */
    function url_has(string $path): bool
    {
        return Route::currentUrlContains($path);
    }
}

if (!function_exists('url_starts_with')) {
    /**
     * 현재 URL이 특정 문자열로 시작하는지 확인
     *
     * @param string $path
     * @return bool
     */
    function url_starts_with(string $path): bool
    {
        return Route::currentUrlStartsWith($path);
    }
}

if (!function_exists('routes')) {
    /**
     * 등록된 전체 라우트 컬렉션 반환
     *
     * @return RouteCollection
     */
    function routes(): RouteCollection
    {
        return Route::getRoutes();
    }
}

if (!function_exists('router')) {
    /**
     *
     * @return RouterInterface
     */
    function router(): RouterInterface
    {
        return app(RouterInterface::class);
    }
}

function locale_route(string $name, array $params = [])
{
    $url = route($name, $params);
    $locale = get_locale(); // ko, en 등

    return '/' . $locale . ltrim($url, '/');
}

if (!function_exists('route_param')) {
    /**
     * 현재 라우트에서 파라미터 값을 가져온다 (예: /users/{id} → route_param('id'))
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function route_param(string $key, $default = null)
    {
        $route = request()->route();

        if ($route === null) {
            return $default;
        }

        return $route->parameter($key, $default);
    }
}


if (!function_exists('route_name')) {
    /**
     * 현재 라우트 이름 반환
     *
     * @return string|null
     */
    function route_name(): ?string
    {
        $route = request()->route();
        return $route ? $route->getName() : null;
    }
}

if (!function_exists('route_is')) {
    /**
     * 현재 라우트 이름이 특정 prefix로 시작하는지 검사
     *
     * @param string $prefix
     * @return bool
     */
    function route_is(string $prefix): bool
    {
        $name = route_name();
        return $name !== null && strpos($name, $prefix) === 0;
    }
}


if (!function_exists('route_with_query')) {
    function route_with_query(string $name, array $params = []): string
    {
        $base = route($name, $params);
        $query = http_build_query($_GET);
        return $query ? "{$base}?{$query}" : $base;
    }
}