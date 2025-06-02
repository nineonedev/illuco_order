<?php

namespace Framework\Bus\Contracts; 

interface QueueableInterface
{
    public function queue(): string; 

    public function delay(): int; 
}