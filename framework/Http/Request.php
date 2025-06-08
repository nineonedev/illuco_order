<?php 

namespace Framework\Http;

use Framework\Routing\Route;
use Framework\Validation\Validator;

class Request {
    
    protected array $get; 
    protected array $post; 
    protected array $cookies; 
    protected array $files; 
    protected array $data; 
    
    protected Http $http;
    protected ?Route $route = null; 
    protected ?Validator $validator = null;

    public function __construct(
        array $get = [], 
        array $post = [],
        array $cookies = [], 
        array $files = [], 
        array $server = []
    )
    {
        $this->http = new Http($server); 
        $this->post = $this->parseJsonBody($server) ?? $post;
        $this->get = $get; 
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

    protected function parseJsonBody(array $server): ?array
    {
        $contentType = $server['CONTENT_TYPE'] ?? '';

        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true);

            if (is_array($json)) {
                return $json;
            }
        }
        
        return null;
    }

    public function isJsonRequest(): bool
    {
        return (
            $this->http()->isAjax()
            || stripos(request()->http()->accept() ?? '', 'application/json') !== false
        );
    }

    public function merge(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
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
        return $this->data[$name] ?? null;
    }

    public function header(string $key, $default = null)
    {
        return $this->http->header($key, $default); 
    }

    public function method(): string
    {
        $method = $this->body('_method');
        $method = $method ? strtoupper($method) : null;

        if ($method && in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $method;
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

    public function file(string $key)
    {
        return $this->files[$key] ?? null;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
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

    protected function ensureValidator(array $rules)
    {
        if (!$this->validator) {
            $this->validator = Validator::make($this->all(), $rules); 
        }
        
        $this->validator->addRules($rules);
    }

    public function validate(array $rules): bool
    {
        $this->ensureValidator($rules);
        return $this->validator->validate();
    }
    
    public function validateOrFail(array $rules): void
    {
        $this->ensureValidator($rules); 
        $this->validator->validateOrFail();
    }
    
    public function replace(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function validated(): array
    {
        if (!$this->validator) {
            throw new \RuntimeException('No validation has been performed on this request.');
        }
        return $this->validator->validated();
    }

    public function safe(array $only = []): array
    {
        if (!$this->validator) {
            throw new \RuntimeException('No validation has been performed on this request.');
        }
        $data = $this->validator->validated();

        if (!empty($only)) {
            return array_intersect_key($data, array_flip($only));
        }
        return $data;
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