<?php

namespace Framework\Bus; 

class EventServiceProvider
{
    /**
     * @var array<string,array<int,string|\Closure>>
     */
    protected array $listen = [];

    protected EventDispatcher $dispatcher; 

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher; 
    }

    public function setListen(array $listen): void
    {
        $this->listen = $listen; 
    }

    public function register(): void
    {
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this->dispatcher->listen($event, $listener);
            }
        }
    }
}