<?php 

namespace Framework\Http; 

class Http 
{
    protected array $server; 

    public function __construct(array $server = [])
    {
        $this->server = $server ?: $_SERVER;
    }

    public function server(): array
    {
        return $this->server;
    }

    public function isSecure(): bool
    {
        return (
            !empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off'
        ) || (isset($this->server['SERVER_PORT']) && $this->server['SERVER_PORT'] == 443);
    }

    public function schema(): string
    {
        return $this->isSecure() ? 'https' : 'http'; 
    }

    public function header(string $key, $default = null)
    {
        $key = strtoupper(str_replace('-', '_', $key)); 

        $headers = $this->server; 

        if (isset($headers['HTTP_' . $key])) {
            return $headers['HTTP_' . $key]; 
        }

        $special = ['CONTENT_TYPE', 'CONTENT_LENGTH']; 
        if (in_array($key, $special) && isset($headers[$key])) {
            return $headers[$key]; 
        }

        return $default; 
    }

    public function host(): ?string
    {
        return $this->server['HTTP_HOST'] ?? $this->server['SERVER_NAME'] ?? null; 
    }

    public function port(): ?int
    {
        return (int) ($this->server['SERVER_PORT'] ?? 80); 
    }

    public function uri(): ?string
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    public function path(): string
    {
        return parse_url($this->uri(), PHP_URL_PATH) ?? '/';
    }

    public function queryString(): ?string
    {
        return $this->server['QUERY_STRING'] ?? null;
    }

    public function fullUrl(): string
    {
        $query = $this->queryString();
        $uri = $this->path(); 

        return $this->schema() . '://' . $this->host() . $uri . ($query ? "?{$query}" : '');
    }

    public function referer(): ?string
    {
        return $this->server['HTTP_REFERER'] ?? null; 
    }

    public function userAgent(): ?string
    {
        return $this->server['HTTP_USER_AGENT'] ?? null; 
    }

    public function ip(): ?string
    {
        return $this->server['HTTP_CLIENT_IP']
            ?? $this->server['HTTP_X_FORWARDED_FOR']
            ?? $this->server['REMOTE_ADDR']
            ?? null;
    }

    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function isMethod(string $method): bool
    {
        return strtoupper($method) === $this->method(); 
    }

    public function protocol(): ?string
    {
        return $this->server['SERVER_PROTOCOL'] ?? null; 
    }

    public function contentType(): ?string
    {
        return $this->server['CONTENT_TYPE'] ?? null; 
    }

    public function accept(): ?string
    {
        return $this->server['HTTP_ACCEPT'] ?? null; 
    }

    public function isAjax(): bool
    {
        return strtolower($this->server['HTTP_X_REQUEST_WITH'] ?? '') === 'xmlhttprequest'; 
    }

    public function all(): array
    {
        return $this->server;
    }
} 