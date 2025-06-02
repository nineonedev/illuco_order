<?php

namespace Framework\Console;

use Framework\Console\Exceptions\CommandNotFoundException;

class CommandCollection 
{
    /**
     * @var array<string, Command>
     */
    protected array $commands = [];

    /**
     * @param class-string<Command>|Command> $command
     */
    public function add($command): void
    {
        if (is_string($command)) {  
            $command = new $command();
        }

        $signature = $command->getSignature(); 

        if (!$signature) {
            throw new \InvalidArgumentException("Command signature is required."); 
        }

        $this->commands[$signature] = $command;
    }

    /**
     * Summary of addMany
     * @param array<int,class-string<Command>|Command> $commands
     */
    public function addMany($commands): void
    {
        foreach ($commands as $command) {       
            $this->add($command);
        }
    }

    public function has(string $signature): bool
    {
        return isset($this->commands[$signature]); 
    }

    public function get(string $signature): Command
    {
        if (!$this->has($signature)) {
            throw new CommandNotFoundException("Command '{$signature}' not found."); 
        }

        return $this->commands[$signature]; 
    }

    /**
     * @return Command[]
     */
    public function all(): array
    {
        return $this->commands;
    }
}