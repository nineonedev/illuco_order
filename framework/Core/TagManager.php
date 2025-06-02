<?php 

namespace Framework\Core;

class TagManager
{
    protected array $tags = [];
    protected Container $container; 
    
    public function __construct(Container $container)
    {
        $this->container = $container; 
    }
    
    public function tag(string $tag, string $abstract): void
    {
        $this->tags[$tag][] = $abstract; 
    }

    public function tags(string $tag, array $abstracts): void
    {
        foreach ($abstracts as $abstract) {
            $this->tag($tag, $abstract);
        }
    }

    public function tagged(string $tag): array
    {
        $results = [];
        
        foreach ($this->tags[$tag] ?? [] as $abstract) {
            $results[] = $this->container->make($abstract);
        }

        return $results;
    }
}