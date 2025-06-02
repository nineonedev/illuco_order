<?php

namespace Framework\Bus;

use Framework\Bus\Contracts\EventInterface;
use Framework\Bus\Contracts\JobInterface;
use Framework\Bus\Contracts\ListenerInterface;

class ListenerJob implements JobInterface
{
    protected ListenerInterface $listener;
    protected EventInterface $event; 

    public function __construct(
        ListenerInterface $listener,
        EventInterface $event
    )
    {
        $this->listener = $listener; 
        $this->event = $event; 
    }

    public function handle(): void
    {
        $this->listener->handle($this->event); 
    }

    public function queue(): string
    {
        return $this->listener instanceof QueueableListener
            ? $this->listener->queue()
            : 'default'; 
    }

    public function delay(): int
    {
        return $this->listener instanceof QueueableListener
            ? $this->listener->delay()
            : 0;
    }
}