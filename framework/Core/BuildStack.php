<?php 

namespace Framework\Core;

class BuildStack 
{
    protected $stack = [];
    
    public function getStack(): array
    {
        return $this->stack;
    }

    public function push(string $concrete)
    {
        $this->stack[] = $concrete;
    }

    public function pop(): string
    {
        return array_pop($this->stack);
    }

    public function in(string $concrete): bool
    {
        return in_array($concrete, $this->stack);
    }
}