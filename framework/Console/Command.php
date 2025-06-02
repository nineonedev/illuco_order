<?php

namespace Framework\Console;

use Framework\Console\Contracts\CommandInterface;
use Framework\Console\Input\Input;
use Framework\Console\Output\Output;
use Framework\Console\Support\CommandDefinition;

abstract class Command implements CommandInterface
{
    protected string $signature = '';
    protected string $description = '';
    protected bool $shouldLock = false;

    protected CommandDefinition $definition;
    protected Input $input;
    protected Output $output;

    protected array $arguments = [];
    protected array $argumentOrder = [];
    protected array $options = [];

    public function __construct()
    {
        $this->definition = new CommandDefinition();
        $this->configure();
    }

    public function shouldLock(): bool
    {
        return $this->shouldLock;
    }

    public function signature(): string
    {
        return $this->signature;
    }

    public function description(): string
    {
        return $this->description;
    }

    abstract protected function handle(): void;

    /**
     * 자식 클래스에서 인자 및 옵션 정의
     */
    protected function configure(): void
    {
        // override if needed
    }

    protected function addArgument(string $name, $default = null): void
    {
        $this->definition->addArgument($name, $default);
    }

    protected function addOption(string $name, $default = null): void
    {
        $this->definition->addOption($name, $default);
    }

    protected function argument($key, $default = null)
    {
        return $this->definition->argument($key, $default);
    }

    protected function option(string $key, $default = null)
    {
        return $this->definition->option($key, $default);
    }

    public function execute(Input $input, Output $output): void
    {
        $this->input = $input;
        $this->output = $output;

        $this->definition->bind($input);

        $this->handle();
    }

    // 출력 헬퍼

    protected function line(string $message): void
    {
        $this->output->writeln($message);
    }

    protected function info(string $message): void
    {
        $this->output->info($message);
    }

    protected function error(string $message): void
    {
        $this->output->error($message);
    }

    protected function warn(string $message): void
    {
        $this->output->warn($message);
    }

    // 문자열 변환 헬퍼

    protected function snake(string $name): string
    {
        $name = preg_replace('/([a-z])([A-Z])/', '$1_$2', $name);
        return strtolower(str_replace([' ', '-'], '_', $name));
    }

    protected function classify(string $name): string
    {
        $name = str_replace(['-', '_'], ' ', $name);
        return str_replace(' ', '', ucwords($name));
    }
}
