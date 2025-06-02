<?php

namespace Framework\Core;

class Binding 
{
    protected string $abstract;

    /** @var mixed $concrete */ 
    protected $concrete; 

    protected bool $shared = false;

    public function __construct(string $abstract, $concrete, bool $shared = false)
    {
        $this->abstract = $abstract; 
        $this->concrete = $concrete;
        $this->shared = $shared;     
    }

    /**
     * @return mixed
     */
    public function getConcrete()
    {
        return $this->concrete; 
    }

    public function isShared(): bool
    {
        return $this->shared;
    }

    public function getAbstract(): string
    {
        return $this->abstract;
    }
}