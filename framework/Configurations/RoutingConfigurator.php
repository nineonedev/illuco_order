<?php 

namespace Framework\Configurations; 

class RoutingConfigurator
{
    protected ?string $web = null;
    protected ?string $console = null; 
    protected ?string $health = null; 

    public function web(string $path): void
    {
        $this->web = $path; 
    }

    public function console(string $path): void
    {
        $this->console = $path; 
    }

    public function health(string $uri): void
    {
        $this->health = $uri; 
    }

    public function getWeb(): ?string
    {
        return $this->web; 
    }

    public function getConsole(): ?string
    {
        return $this->console; 
    }

    public function getHealth(): ?string
    {
        return $this->health; 
    }

    public function load()
    {
        foreach ([
            $this->web,  
            $this->console, 
            $this->health
        ] as $file) {
            if ($file && file_exists($file)) {
                require_once $file; 
            }
        }
    }
}