<?php

namespace Framework\Http\Contracts;

interface ResponseInterface
{
    public function setStatusCode(int $statusCode);
    public function getStatusCode(): int;
    public function getHeaders(): array;
    public function setHeader(string $key, string $value);
    public function setHeaders(array $headers);
    public function withCookie(string $name, string $value, int $minutes = 60, array $options = []);
    public function withInput(array $input = []);
    public function withErrors(array $errors);
    public function withSession(string $key, $value);
    public function getContent();
    public function setContent($content);
    public function send(): void;
}
