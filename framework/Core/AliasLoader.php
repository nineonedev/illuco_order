<?php 

namespace Framework\Core;

class AliasLoader
{
    protected Container $container;
    protected array $aliases = []; 
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function setAliases(array $aliases): void
    {
        $this->aliases = array_merge($this->aliases, $aliases); 
    }

    public function set(string $class, string $alias): void
    {
        $this->aliases[$class] = $alias; 
    }

    public function get(string $alias): ?string
    {
        return $this->aliases[$alias] ?? $alias; 
    }

    public function all(): array
    {
        return $this->aliases; 
    }

    public function has(string $alias): bool
    {
        return array_key_exists($alias, $this->aliases); 
    }

    public function register(): void
    {
        foreach ($this->aliases as $class => $alias) {
            if (!class_exists($alias) && class_exists($class)) {
                class_alias($class, $alias);
            }
        }
    }
}