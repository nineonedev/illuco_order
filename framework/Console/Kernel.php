<?php

namespace Framework\Console;

use Framework\Core\Contracts\KernelInterface;
use Framework\Support\LockManager;
use Throwable;

class Kernel implements KernelInterface
{
    protected CommandCollection $commands;
    protected Output $output;
    protected LockManager $lockManager;

    public function __construct(CommandCollection $commands, LockManager $lockManager)
    {
        $this->commands = $commands;
        $this->output = new Output();
        $this->lockManager = $lockManager;
    }

    public function register(Command $command): void
    {
        $this->commands->add($command);
    }

    /**
     * @param CommandInput $input
     */
    public function handle($input): void
    {
        $signature = $input->getCommand();

        if (!$this->commands->has($signature)) {
            $this->output->error("Command '{$signature}' not found.");
            $this->list();
            return;
        }

        $command = $this->commands->get($signature);
        $command->setInput($input);
        $command->setOutput($this->output);

        try {
            $command->handle();
        } catch (Throwable $e) {
            $this->output->error("Error: " . $e->getMessage());
        }
    }

    /**
     * @param CommandInput $input
     */
    public function terminate($input, $result = null): void
    {
        $this->lockManager->get($input->getCommand())->release();
    }

    public function list(): void
    {
        $this->output->writeln("Available commands:");
        foreach ($this->commands->all() as $signature => $command) {
            $desc = $command->getDescription();
            $this->output->writeln("  {$signature}    {$desc}");
        }
    }
}

