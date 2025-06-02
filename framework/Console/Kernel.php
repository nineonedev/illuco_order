<?php

namespace Framework\Console;

use Framework\Console\Input\Input;
use Framework\Console\Output\Output;
use Framework\Console\Exceptions\CommandNotFoundException;
use Framework\Console\Lock\LockManager;
use Framework\Core\Contracts\KernelInterface;

class Kernel implements KernelInterface
{
    protected CommandRegistry $registry;
    protected Output $output;
    protected LockManager $locks;

    public function __construct(
        CommandRegistry $registry, 
        ?Output $output = null,
        ?LockManager $locks = null
    )
    {
        $this->registry = $registry;
        $this->output = $output ?: new Output();
        $this->locks = $locks ?: new LockManager();
    }

    /**
     * 콘솔 입력을 처리합니다.
     *
     * @param Input $input
     * @return void
     */
    public function handle($input)
    {
        if (!($input instanceof Input)) {
            throw new \InvalidArgumentException('Console Kernel only accepts Input instance.');
        }

        $signature = $input->getCommand();

        try {
            /** @var Command $command */
            $command = $this->registry->get($signature);
        } catch (CommandNotFoundException $e) {
            $this->output->error("Command [{$signature}] not found.");
            $this->listCommands();
            return;
        }

        if ($command->shouldLock()) {
            $lock = $this->locks->get($signature); 

            if (!$lock->acquire()) {
                $this->output->warn("Command [{$signature}] is already running."); 
                return; 
            }
        }

        $command->execute($input, $this->output);
    }

    /**
     * 종료 후 후처리 로직.
     *
     * @param Input $input
     * @param mixed|null $result
     * @return void
     */
    public function terminate($input, $result = null): void
    {
        // 예: 로그 남기기, 록 해제, 종료 메시지 출력 등
        if ($input instanceof Input) {
            $signature = $input->getCommand(); 
            $this->locks->get($signature)->release();
        }
    }

    /**
     * 등록된 커맨드 전체 출력
     */
    protected function listCommands(): void
    {
        $this->output->writeln('');
        $this->output->writeln("Available commands:");

        foreach ($this->registry->all() as $command) {
            $this->output->writeln(sprintf("  %-20s %s", $command->signature(), $command->description()));
        }

        $this->output->writeln('');
    }
}
