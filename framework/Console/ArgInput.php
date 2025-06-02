<?php

namespace Framework\Console;

class ArgInput
{
    protected CommandInput $input;

    public function __construct(CommandInput $input)
    {
        $this->input = $input;
    }

    public function argument(int $index, $default = null)
    {
        return $this->input->getArgument($index, $default);
    }

    public function hasArgument(int $index): bool
    {
        return $this->argument($index) !== null;
    }

    public function option(string $key, $default = null)
    {
        return $this->input->getOption($key, $default);
    }

    public function hasOption(string $key): bool
    {
        return $this->input->hasOption($key);
    }

    public function command(): string
    {
        return $this->input->getCommand();
    }
}
