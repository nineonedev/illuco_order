<?php

namespace Framework\Bus;

use Closure;
use Framework\Bus\Contracts\EventInterface;
use Framework\Bus\Contracts\ListenerInterface;
use InvalidArgumentException;

class ListenerResolver
{
    /**
     * @param string|\Closure $listener
     */
    public function resolve($listener): ListenerInterface
    {
        if ($listener instanceof Closure) {
            return new class($listener) implements ListenerInterface {
                protected $callback; 

                public function __construct(Closure $callback)
                {
                    $this->callback = $callback; 
                }

                public function handle(EventInterface $event): void
                {
                    ($this->callback)($event); 
                }
            };
        }

        if (is_string($listener) && class_exists($listener)) {
            $instance = app($listener); 

            if (!$instance instanceof ListenerInterface) {
                throw new InvalidArgumentException("Listener [$listener] must be implement ListenerInterface.");
            }

            return $instance; 
        }

        throw new InvalidArgumentException("Invalid listener provided."); 
    }
}