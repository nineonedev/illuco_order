<?php

namespace Framework\Core\Contracts;

use Framework\Console\CommandInput;
use Framework\Http\Request;

interface ApplicationInterface
{
    public function handleRequest(Request $request): void;

    public function handleCommand(CommandInput $input): void;

    public function register(): void;

    public function boot(): void;

    public function booted(callable $callback): void;

    public function booting(callable $callback): void;

    public function isBooted(): bool;
    
    public function getEnvironment(): string;

    public function setEnvironment(string $env): void;
}