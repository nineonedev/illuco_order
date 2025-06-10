<?php 

namespace Framework\Core;

use Closure;
use Framework\Core\Exceptions\ContainerException;
use Framework\Support\Str;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;

class Reflector 
{
    protected BuildStack $buildStack;
    protected Container $container; 

    protected $with = [];

    public function __construct(Container $container)
    {
        $this->container = $container; 
        $this->buildStack = new BuildStack();
    }

    /**
     * @param callable|array|string $callback
     * @return mixed
     */
    public function call($callback, array $parameters = [])
    {
        if (!empty($parameters)) {
            $this->with = $parameters; 
        }

        if (is_array($callback)) {
            [$class, $method] = $callback; 
            $instance = is_string($class) ? $this->container->make($class) : $class;
            $reflector = new ReflectionMethod($instance, $method); 
            $dependencies = $this->resolveDependencies($reflector->getParameters()); 
            return $reflector->invokeArgs($instance, $dependencies);
        }

        if (is_string($callback) && Str::contains($callback, '@')) {
            [$class, $method] = explode('@', $callback); 
            return $this->call([$class, $method], $parameters); 
        }

        if ($callback instanceof Closure || is_callable($callback)) {
            $reflector = new ReflectionFunction($callback); 
            $dependencies = $this->resolveDependencies($reflector->getParameters()); 
            return $reflector->invokeArgs($dependencies); 
        }

        throw new ContainerException("Invalid callable passed to call().");
    }

    /**
     * @param Closure|string $concrete
     * @return mixed
     */
    public function build($concrete, array $parameters = [])
    {
        if (!empty($parameters)) {
            $this->with = $parameters; 
        }

        if ($concrete instanceof Closure) {
            return $concrete($this->container, $parameters); 
        }

        if (!class_exists($concrete)) {
            throw new ContainerException("Cannot resolve {$concrete}."); 
        }

        $reflector = new ReflectionClass($concrete); 

        if (!$reflector->isInstantiable()) {
            throw new ContainerException("Class {$concrete} is not instantiable."); 
        }

        $className = $reflector->getName(); 

        if ($this->buildStack->in($className)) {
            throw new ContainerException("Circular dependency detected: {$className}"); 
        }

        $this->buildStack->push($className); 

        $constructor = $reflector->getConstructor();
        $dependencies = $constructor
            ? $this->resolveDependencies($constructor->getParameters())
            : []; 

        $this->buildStack->pop();

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * @return array
     */
    public function resolveDependencies(array $parameters = [])
    {
        $with = $this->with; 
        $dependencies = []; 
        
        foreach ($parameters as $parameter) {
            /** @var ReflectionParameter $parameter */
            
            $name = $parameter->getName(); 
            $type = $parameter->getType();

            if (array_key_exists($name, $with)) {
                $dependencies[] = $with[$name];
                continue; 
            }

            if (!$type) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue(); 
                    continue; 
                }

                throw new ContainerException("Cannot resolve parameter \${$name} without type hint.");
            }

            $typeName = $type->getName(); 

            if ($type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new ContainerException("Cannot resolve builtin type \${$name} ({$typeName}).");
            }

            $given = $this->container->getContextBinding(
                $typeName,
                $this->buildStack->getStack()
            );

            $dependencies[] = $given !== null
                ? $given->getImplementation()
                : $this->container->make($typeName);

        }

        $this->with = [];
        return $dependencies;
    }
}