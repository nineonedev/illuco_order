<?php

namespace Framework\Http;

class Redirector
{
    public function to(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }

    public function back(int $status = 302): Response
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return $this->to($referer, $status);
    }

    public function home(int $status = 302): Response
    {
        return $this->to('/', $status);
    }

    public function with(string $key, $value): Response
    {
        return $this->to($this->previous())->with($key, $value); 
    }

    public function withErrors(array $errors): Response
    {
        return $this->to($this->previous())->withErrors($errors);
    }

    public function withInput(array $input): Response
    {
        return $this->to($this->previous())->withInput($input);
    }

    public function previous(): string
    {
        return $_SERVER['HTTP_REFERER'] ?? '/';
    }

    public function intended(string $default = '/', int $status = 302): Response
    {
        $intended = $_SESSION['_intended'] ?? $default;
        unset($_SESSION['_intended']);
        return $this->to($intended, $status);
    }

    public function refresh(): Response
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return $this->to($uri);
    }

}
