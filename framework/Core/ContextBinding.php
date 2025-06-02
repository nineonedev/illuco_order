<?php 

namespace Framework\Core;

class ContextBinding
{
    protected Contextual $contextual; 
    protected string $when;
    protected string $needs;
    protected string $give;


    public function __construct(Contextual $contextual, string $service)
    {
        $this->contextual = $contextual;
        $this->when = $service;
    }

    /**
     * @return static
     */
    public static function create(Contextual $contextual, string $service)
    {
        return new static($contextual, $service);
    }

    /**
     * @return $this
     */
    public function needs(string $abstract)
    {
        $this->needs = $abstract; 
        return $this; 
    }

    /**
     * @param mixed $implementation
     */
    public function give($implementation): void
    {
        $this->give = $implementation;
        $this->contextual->add($this);
    }

    public function isPrepared(): bool
    {
        return $this->when && $this->needs && $this->give;
    }

    public function getService(): string
    {
        return $this->when;
    }

    public function getAbstract(): string
    {
        return $this->needs;
    }

    /**
     * @return mixed
     */
    public function getImplementation()
    {
        return $this->give;
    }
}