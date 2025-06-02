<?php

namespace Framework\Bus;

use Framework\Bus\Contracts\EventInterface;
use Framework\Bus\Contracts\QueueableInterface;

class EventDispatcher
{
    protected array $listeners = []; 

    protected ListenerResolver $resolver; 

    protected QueueManager $queue; 

    public function __construct(
        ListenerResolver $resolver,
        QueueManager $queue
    )
    {
        $this->resolver = $resolver;
        $this->queue = $queue; 
    }

    /**
     * @param string|\Closure $listener
     */
    public function listen(string $eventName, $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    public function dispatch(EventInterface $event): void
    {
        $eventName = $event->name(); 

        foreach ($this->listeners[$eventName] ?? [] as $listenerDef) {
            $listener = $this->resolver->resolve($listenerDef);

            if ($listener instanceof QueueableInterface) {
                $this->queue->push(new ListenerJob($listener, $event)); 
            } else {
                $listener->handle($event); 
            }
        }
    }
}