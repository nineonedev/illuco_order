<?php

namespace Framework\Console\Input;

class Input
{
    protected string $command;
    protected array $arguments = [];
    protected array $options = [];

    /**
     * @param string $command
     * @param array $arguments
     * @param array $options
     */
    public function __construct(string $command, array $arguments = [], array $options = [])
    {
        $this->command = $command;
        $this->arguments = $arguments;
        $this->options = $options;
    }

    public static function capture(): self
    {
        global $argv; 
        
        return static::parse($argv);
    }

    /**
     * 전체 argv로부터 Input 객체 생성
     */
    public static function parse(array $argv): self
    {
        array_shift($argv); // remove script path
        $command = array_shift($argv) ?? '';

        $arguments = [];
        $options = [];

        foreach ($argv as $arg) {
            if (strpos($arg, '--') === 0) {
                [$key, $value] = explode('=', substr($arg, 2), 2) + [1 => true];
                $options[$key] = $value;
            } else {
                $arguments[] = $arg;
            }
        }

        return new self($command, $arguments, $options);
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getArgument(int $index, $default = null)
    {
        return $this->arguments[$index] ?? $default;
    }

    public function hasArgument(int $index): bool
    {
        return array_key_exists($index, $this->arguments);
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
