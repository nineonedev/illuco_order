<?php

namespace Framework\Bus\Contracts; 

interface ListenerInterface
{
    public function handle(EventInterface $event): void; 
}