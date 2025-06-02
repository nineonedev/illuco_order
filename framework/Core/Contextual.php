<?php 

namespace Framework\Core;

class Contextual {
    protected Container $container;

    /**
     * @var array<ContextBinding>
     */
    protected array $bindings = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function create(string $service): ContextBinding
    {   
        return ContextBinding::create($this, $service);
    }

    public function add(ContextBinding $contextBinding)
    {
        $this->bindings[] = $contextBinding;
    }

    public function has(string $service, string $abstract): bool
    {
        return !is_null($this->get($service, $abstract));
    }

    public function get(string $service, string $abstract): ?ContextBinding
    {
        foreach ($this->bindings as $binding) {
            /** @var ContextBinding $binding */

            if (!$binding->isPrepared()) {
                continue; 
            }

            if ($binding->getService() === $service 
            && $binding->getAbstract() === $abstract) {
                return $binding;
            }
        }

        return null;
    }
}