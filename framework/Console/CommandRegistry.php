<?php

namespace Framework\Console;

use Framework\Console\Contracts\CommandInterface;
use Framework\Console\Exceptions\CommandNotFoundException;

class CommandRegistry
{
    /**
     * @var array<string, CommandInterface>
     */
    protected array $commands = [];

    /**
     * 커맨드 인스턴스를 등록합니다.
     *
     * @param CommandInterface|class-string<CommandInterface> $command
     * @return void
     */
    public function add($command): void
    {
        if (is_string($command)) {
            $command = new $command();
        }
        
        $signature = $command->signature();

        if (!$signature) {
            throw new \InvalidArgumentException('Command signature is required.');
        }

        $this->commands[$signature] = $command;
    }

    /**
     * 여러 개의 커맨드를 한 번에 등록합니다.
     *
     * @param iterable<CommandInterface|class-string<CommandInterface>> $commands
     * @return void
     */
    public function addMany(iterable $commands): void
    {
        foreach ($commands as $command) {
            $this->add($command);
        }
    }

    /**
     * 커맨드 존재 여부 확인
     *
     * @param string $signature
     * @return bool
     */
    public function has(string $signature): bool
    {
        return isset($this->commands[$signature]);
    }

    /**
     * 커맨드 조회
     *
     * @param string $signature
     * @return CommandInterface
     *
     * @throws CommandNotFoundException
     */
    public function get(string $signature): CommandInterface
    {
        if (!$this->has($signature)) {
            throw new CommandNotFoundException("Command [{$signature}] not found.");
        }

        return $this->commands[$signature];
    }

    /**
     * 등록된 모든 커맨드를 반환
     *
     * @return CommandInterface[]
     */
    public function all(): array
    {
        return array_values($this->commands);
    }
}
