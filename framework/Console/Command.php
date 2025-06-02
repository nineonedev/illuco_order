<?php

namespace Framework\Console;

abstract class Command
{
    protected CommandInput $input;
    protected Output $output;

    protected string $signature = '';
    protected string $description = '';
    protected bool $shouldUseLock = true;

    abstract public function handle(): void;

    public function shouldUseLock(): bool
    {
        return $this->shouldUseLock;
    }

    public function getArgument(string $key): ?string
    {
        return $this->input->getArgument($key);
    }

    public function getOption(string $key): ?string
    {
        return $this->input->getOption($key);
    }

    public function setInput(CommandInput $input): void
    {
        $this->input = $input;
    }

    public function setOutput(Output $output): void
    {
        $this->output = $output;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function info(string $message): void
    {
        $this->output->info($message);
    }

    public function error(string $message): void
    {
        $this->output->error($message);
    }

    public function success(string $message): void
    {
        $this->output->success($message);
    }

    public function warning(string $message): void
    {
        $this->output->warning($message);
    }

    public function line(string $message): void
    {
        $this->output->writeln($message);
    }
}
