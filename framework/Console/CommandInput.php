<?php

namespace Framework\Console;

class CommandInput
{
    protected string $command;
    protected array $arguments;
    protected array $options;

    public function __construct(string $command, array $arguments = [], array $options = [])
    {
        $this->command = $command;
        $this->arguments = $arguments;
        $this->options = $options;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getArgument($key, $default = null)
    {
        return $this->arguments[$key] ?? $default;
    }

    public function hasArgument($key): bool
    {
        return array_key_exists($key, $this->arguments);
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    public function hasOption(string $key): bool
    {
        return array_key_exists($key, $this->options);
    }
}
