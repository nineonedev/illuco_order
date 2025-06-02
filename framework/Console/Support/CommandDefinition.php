<?php

namespace Framework\Console\Support;

use Framework\Console\Input\Input;
use InvalidArgumentException;

class CommandDefinition
{
    protected array $arguments = [];       // name => default
    protected array $argumentOrder = [];   // index => name
    protected array $options = [];         // name => default

    protected Input $input;

    public function addArgument(string $name, $default = null): void
    {
        $index = count($this->argumentOrder);
        $this->argumentOrder[$index] = $name;
        $this->arguments[$name] = $default;
    }

    public function addOption(string $name, $default = null): void
    {
        $this->options[$name] = $default;
    }

    public function bind(Input $input): void
    {
        $this->input = $input;
    }

    /**
     * @param string|int $key
     */
    public function argument($key, $default = null)
    {
        $index = is_numeric($key)
            ? (int) $key
            : array_search($key, $this->argumentOrder);

        $name = is_numeric($key)
            ? $this->argumentOrder[$key] ?? null
            : $key;

        if ($name === null || $index === false) {
            return $default;
        }

        return $this->input->getArgument($index, $this->arguments[$name] ?? $default);
    }

    public function option(string $key, $default = null)
    {
        return $this->input->getOption($key, $this->options[$key] ?? $default);
    }

    public function getDefinedArguments(): array
    {
        return $this->arguments;
    }

    public function getDefinedOptions(): array
    {
        return $this->options;
    }
}
