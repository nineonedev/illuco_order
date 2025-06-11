<?php

use Framework\Http\Response;
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


if (!function_exists('redirect_route')) {
    /**
     * 라우트 이름 기반으로 리다이렉트(302) 응답 반환
     *
     * @param string $name
     * @param array $params
     * @param int $status
     * @return Response
     */
    function redirect_route(string $name, array $params = [], int $status = 302)
    {
        $url = route($name, $params);
        return response()->redirect($url, $status);
    }
}