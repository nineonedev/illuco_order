<?php 

namespace Framework\Core; 

class Binder 
{
    protected Container $container;
    
    /**
     * @var array<Binding>
     */
    protected $bindings = [];
    protected $instances = []; 

    public function __construct(Container $container)
    {
        $this->container = $container; 
    }

    public function bind(string $abstract, $concrete = null): void
    {
        $this->bindings[] = new Binding($abstract, $concrete, false);
    }

    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bindings[] = new Binding($abstract, $concrete, true);
    }
    
    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance; 
    }
    
    public function removeInstance(string $abstract): void
    {
        unset($this->instances[$abstract]);
    }
    
    public function clearInstances(): void
    {
        $this->instances = [];
    }

    public function getInstance(string $abstract): ?object
    {
        return $this->instances[$abstract] ?? null;
    }

    public function boundInstance(string $abstract): bool
    {
        return isset($this->instances[$abstract]);
    }

    public function isShared(string $abstract): bool
    {
        $abstract = $this->container->getAlias($abstract); 

        foreach ($this->bindings as $binding) {
            /** @var Binding $binding */

            if ($binding->getAbstract() === $abstract && $binding->isShared()) {
                return true; 
            }
        }

        return false; 
    }

    public function getBinding(string $abstract): ?Binding
    {
        $abstract = $this->container->getAlias($abstract, $abstract);
        
        foreach ($this->bindings as $binding) {
            /** @var Binding $binding */

            if ($binding->getAbstract() === $abstract) {
                return $binding;
            }
        }
        
        return null; 
    }

    public function bound(string $abstract): bool
    {
        $abstract = $this->container->getAlias($abstract, $abstract);
        
        foreach ($this->bindings as $binding) {
            /** @var Binding $binding */

            if ($binding->getAbstract() === $abstract) {
                return true; 
            }
        }
        
        if (isset($this->instances[$abstract])) {
            return true; 
        }

        return false; 
    }
}