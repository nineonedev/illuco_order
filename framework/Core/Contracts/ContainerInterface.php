<?php 

namespace Framework\Core\Contracts; 

interface ContainerInterface 
{
    public function bind(string $abstract, $concrete = null): void;

    public function singleton(string $abstract, $concrete = null): void;
    
    public function instance(string $abstract, object $instance): void;

    /**
     * @return mixed
     */
    public function make(string $abstract, array $parameters = []);

    /**
     * @return mixed
     */
    public function call(callable $callable, array $parameters = []);
}