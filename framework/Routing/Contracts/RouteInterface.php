<?php

namespace Framework\Routing\Contracts;

interface RouteInterface
{
    public function method(): string;

    public function uri(): string;

    public function action();

    public function name(string $name): self;

    public function getName(): ?string;

    public function middleware($middleware): self;

    public function getMiddleware(): array;

    public function matches(string $method, string $uri): bool;

    public function resolveParametersFromPath(string $path): void;

    public function parameters(): array;

    public function parameter(string $key, $default = null);

    public function where(string $param, string $pattern): self;

    public function whereNumber(string $param): self;

    public function whereAlpha(string $param): self;

    public function whereAlphaNumeric(string $param): self;

    public function whereUuid(string $param): self;

    public function whereUlid(string $param): self;

    public function whereIn(string $param, array $values): self;

    public function getParameterPatterns(): array;
}
