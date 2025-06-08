<?php

namespace Framework\Routing;

class UrlGenerator
{
    protected RouteCollection $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    public function route(string $name, array $parameters = []): string
    {
        $route = $this->routes->getByName($name);

        if (!$route) {
            throw new \InvalidArgumentException("Route [{$name}] not defined.");
        }

        $uri = $route->uri();

        // {param} 또는 {param?} 를 실제 값으로 치환
        $uri = preg_replace_callback('/\{(\w+)\??\}/', function ($matches) use (&$parameters) {
            $key = $matches[1];

            if (array_key_exists($key, $parameters)) {
                $value = $parameters[$key];
                unset($parameters[$key]);
                return urlencode($value);
            }

            // optional parameter: 제거
            return '';
        }, $uri);

        // 남은 파라미터는 쿼리 스트링으로 붙임
        $query = http_build_query($parameters);
        $uri = '/' . trim($uri, '/');

        return $query ? "{$uri}?{$query}" : $uri;
    }
}
