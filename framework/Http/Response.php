<?php

namespace Framework\Http;

use Framework\Support\Facades\View;

class Response
{
    protected string $content = '';
    protected int $statusCode = 200;
    protected array $headers = [];

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->setContent($content);
        $this->setStatusCode($statusCode);

        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function setHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function setHeaders(array $headers): self
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }

        return $this; 
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function withCookie(string $name, string $value, int $minutes = 60): self
    {
        $expire = time() + ($minutes * 60); 
        setcookie($name, $value, $expire, '/'); 
        return $this; 
    }

    public function with(string $key, $value): self
    {
        $_SESSION[$key] = $value; 
        return $this;
    }

    public function withErrors(array $errors): self
    {
        $_SESSION['_errors'] = $errors; 
        return $this;
    }

    public function withInput(array $input = []): self
    {
        $_SESSION['_old_input'] = $input; 
        return $this;
    }

    public static function back(): self
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return (new self('', 302))->setHeader('Location', $referer);
    }


    public function send(): Response
    {
        // 상태 코드
        http_response_code($this->statusCode);

        // 헤더 전송
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        // 본문 출력
        echo $this->content;
        return $this; 
    }

    public static function create(string $content = '', int $statusCode, array $headers = []): self
    {
        return new Response($content, $statusCode, $headers);
    }

    public static function redirect(string $url, int $status = 302): Response
    {
        return (new self('', $status))->setHeader('Location', $url);
    }

    public static function view(string $template, array $data = [], int $statusCode = 200): Response
    {
        $content = View::render($template, $data); 
        return new self($content, $statusCode, ['Content-Type' =>'text/html; charset=utf8']);
    }

    public static function json(array $data, int $statusCode = 200): self
    {
        return new self(
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            $statusCode,
            ['Content-Type' => 'application/json']
        );
    }
}
