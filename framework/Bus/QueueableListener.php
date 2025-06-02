<?php

namespace Framework\Bus;

use Framework\Bus\Contracts\EventInterface;
use Framework\Bus\Contracts\ListenerInterface;
use Framework\Bus\Contracts\QueueableInterface;

abstract class QueueableListener implements ListenerInterface, QueueableInterface
{
    public function queue(): string
    {
        return 'default'; 
    }

    public function delay(): int
    {
        return 0; 
    }

    abstract public function handle(EventInterface $event): void; 
}