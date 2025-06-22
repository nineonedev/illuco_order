<?php

namespace Framework\Http\Responses;

use Framework\Http\Contracts\ResponseInterface;

abstract class AbstractResponse implements ResponseInterface
{
    protected int $statusCode = 200;
    protected array $headers = [];
    protected array $cookies = [];

    protected $content = '';

    public function __construct($content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
    }

    /**
     * @return static
     */
    public function setHeader(string $key, string $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * @return static
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
        return $this;
    }

    /**
     * @return static
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Content getter/setter (공통 제공)
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
    public function getContent()
    {
        return $this->content;
    }

    public function with(string $key, $value)
    {
        $this->withSession($key, $value);
    }

    public function withMany(array $params)
    {
        foreach ($params as $key => $value) {
            $this->withSession($key, $value);
        }
        return $this;
    }

    /**
     * @return static
     */
    public function withCookie(
        string $name,
        string $value,
        int $minutes = 60,
        array $options = []
    ) {
        $this->cookies[] = [
            'name' => $name,
            'value' => $value,
            'minutes' => $minutes,
            'options' => $options
        ];
        cookie()->setWithOption($name, $value, $minutes, $options);
        return $this; 
    }

    public function withInput(array $input = [])
    {
        flash()->input($input);
        return $this;
    }

    public function withErrors(array $errors)
    {
        flash()->errors($errors); 
        return $this;
    }

    public function withSession(string $key, $value)
    {
        flash($key, $value);
        return $this;
    }

    /**
     * @return static
     */
    public function redirect(string $url, int $statusCode = 302)
    {
        return $this->setStatusCode($statusCode)
                    ->setHeader('Location', $url)
                    ->setContent('Redirecting...');
    }

    /**
     * @return static
     */
    public function redirectRoute(string $routeName, array $params = [], int $statusCode = 302)
    {
        $url = route($routeName, $params);
        return $this->redirect($url, $statusCode);
    }

    /**
     * 실제로 HTTP 응답을 전송한다
     */
    public function send(): void
    {
        $this->sendHeaders();
        echo $this->getContent();
    }
    
    protected function sendHeaders(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        
        cookie()->send();
    }
}
