<?php 

namespace Framework\Http;

use Framework\Routing\Route;

class Request {
    
    protected array $get; 
    protected array $post; 
    protected array $cookies; 
    protected array $files; 
    protected array $data; 
    
    protected Http $http;
    protected ?Route $route = null; 

    public function __construct(
        array $get = [], 
        array $post = [],
        array $cookies = [], 
        array $files = [], 
        array $server = []
    )
    {
        $this->http = new Http($server); 
        $this->get = $get; 
        $this->post = $post; 
        $this->cookies = $cookies; 
        $this->files = $files; 
        $this->data = [];
    }

    public static function capture(): self
    {
        return new Request(
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,
            $_SERVER
        );
    }

    public function http(): Http
    {
        return $this->http;
    }

    public function __set($name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        $this->data[$name] ?? null;
    }

    public function header(string $key, $default = null)
    {
        return $this->http->header($key, $default); 
    }

    public function method(): string
    {
        $method = $this->body('_method');

        if (strtoupper($method) && in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])) {
            return strtoupper($method);
        }

        return $this->http->method();
    }

    public function body(string $key, $default = null)
    {
        return $this->post[$key] ?? $default; 
    }

    public function query(string $key, $default = null)
    {
        return $this->get[$key] ?? $default; 
    }

    public function input(string $key, $default = null)
    {
        return $this->body($key)
            ?? $this->query($key)
            ?? $default; 
    }

    public function all()
    {
        return array_merge($this->get, $this->post, $this->data, $this->files); 
    }

    public function has(string $key): bool
    {
        return (bool) ($this->body($key) || $this->query($key));
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->all(), array_flip($keys));
    }

    public function except(array $keys): array
    {
        return array_diff_key($this->all(), array_flip($keys));
    }

    public function path(): string
    {
        return $this->http->path();
    }

    public function old(string $key, $default = null)
    {
        return $_SESSION['_old_input'][$key] ?? $default; 
    }

    public function flash(): array
    {
        return $_SESSION['_old_input'] ?? [];
    }

    public function errors(): array
    {
        return $_SESSION['_errors'] ?? [];
    }

    public function validated()
    {

    }

    public function safe()
    {

    }

    public function setRoute(Route $route): void
    {
        $this->route = $route; 
    }

    public function route(): ?Route
    {
        return $this->route;
    }
}