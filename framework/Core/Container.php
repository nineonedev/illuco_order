<?php 

namespace Framework\Core;

use Framework\Core\Contracts\ContainerInterface;
use Framework\Http\FormRequest;

class Container implements ContainerInterface
{
    protected Contextual $contextual;
    protected AliasLoader $aliasLoader;
    protected TagManager $tagManager; 
    protected Reflector $reflector; 
    protected Binder $binder;

    protected array $resolved = []; 
    protected array $beforeResolvingCallbacks = []; 
    protected array $resolvingCallbacks = []; 
    protected array $afterResolvingCallbacks = []; 

    public function __construct()
    {
        $this->contextual = new Contextual($this); 
        $this->aliasLoader = new AliasLoader($this);
        $this->tagManager = new TagManager($this); 
        $this->reflector = new Reflector($this); 
        $this->binder = new Binder($this);
    }

    public function bind(string $abstract, $concrete = null): void
    {
        $this->binder->bind($abstract, $concrete); 
    }

    public function singleton(string $abstract, $concrete = null): void
    {
        $this->binder->singleton($abstract, $concrete); 
    }
    
    public function instance(string $abstract, object $instance): void
    {
        $this->binder->instance($abstract, $instance); 
    }

    public function isShared(string $abstract): bool
    {
        return $this->binder->isShared($abstract); 
    }

    public function bound(string $abstract): bool
    {
        return $this->binder->bound($abstract);
    }

    public function getContextBinding(string $abstract, array $stack = []): ?ContextBinding
    {
        foreach (array_reverse($stack) as $service) {
            if ($this->contextual->has($service, $abstract)) {
                return $this->contextual->get($service, $abstract);
            }
        }

        return null;
    }

    public function when(string $service): ContextBinding
    {
        return $this->contextual->create($service);
    }

    /**
     * @return mixed
     */
    public function make(string $abstract, array $parameters = [])
    {
        $abstract = $this->getAlias($abstract); 
        return $this->resolve($abstract, $parameters);
    }

    /**
     * @return mixed
     */
    protected function resolve(string $abstract, array $parameters = [])
    {
        $this->fireBeforeResolvingCallbacks($abstract);

        if ($this->binder->boundInstance($abstract)) {
            return $this->binder->getInstance($abstract); 
        }

        $abstract = $this->getAlias($abstract) ?? $abstract; 
        $binding = $this->binder->getBinding($abstract);

        if ($binding !== null) {
            $instance = $this->reflector->build($binding->getConcrete(), $parameters);

            if ($binding->isShared()) {
                $this->instance($abstract, $instance);
                return $instance; 
            }
            
            $this->resolved[$abstract] = true; 
            $this->fireResolvingCallbacksIfExists($abstract, $instance); 
            return $instance;
        }

        $instance = $this->reflector->build($abstract, $parameters); 
        $this->resolved[$abstract] = true; 
        $this->fireResolvingCallbacksIfExists($abstract, $instance); 
        return $instance; 
    }

    protected function fireResolvingCallbacksIfExists(string $abstract, $instance): void
    {
        $this->fireResolvedCallbacks($abstract, $instance, $this->resolvingCallbacks);
        $this->fireResolvedCallbacks($abstract, $instance, $this->afterResolvingCallbacks);
    }


    protected function fireBeforeResolvingCallbacks(string $abstract)
    {
        foreach ($this->beforeResolvingCallbacks as $type => $innerCallbacks) {
            if ($abstract === $type || is_subclass_of($abstract, $type, true)) {
                foreach ($innerCallbacks as $callback) {
                    $callback($abstract, $this);
                }
            }
        }
    }

    protected function fireResolvedCallbacks(string $abstract, object $instance, array $callbacks = []) 
    {
        foreach ($callbacks as $type => $innerCallbacks) {
            if ($abstract === $type || is_subclass_of($abstract, $type, true)) {
                foreach ($innerCallbacks as $callback) {
                    $callback($instance, $this);
                }
            }
        }
    }

    protected function fireResolvingCallbacks(string $abstract, object $instance)
    {
        foreach ($this->resolvingCallbacks[$abstract] as $callback) {
            $callback($instance, $this); 
        }
    }

    protected function fireAfterResolvingCallbacks(string $abstract, object $instance)
    {
        foreach ($this->afterResolvingCallbacks[$abstract] as $callback) {
            $callback($instance, $this); 
        }
    }

    /**
     * @return mixed
     */
    public function call(callable $callable, array $parameters = [])
    {
        return $this->reflector->call($callable, $parameters); 
    }

    public function resolving(string $abstract, callable $callback)
    {
        $this->resolvingCallbacks[$abstract][] = $callback;
    }

    public function beforeResolving(string $abstract, callable $callback)
    {
        $this->beforeResolvingCallbacks[$abstract][] = $callback;
    }

    public function afterResolving(string $abstract, callable $callback)
    {
        $this->afterResolvingCallbacks[$abstract][] = $callback;
    }

    public function tag(string $tag, string $abstract): void
    {
        $this->tagManager->tag($tag, $abstract); 
    }

    public function tags(string $tag, array $abstracts): void
    {
        $this->tagManager->tags($tag, $abstracts);
    }

    public function tagged(string $tag): array
    {
        return $this->tagManager->tagged($tag);
    }

    public function getAlias(string $alias): ?string
    {
        return $this->aliasLoader->get($alias);
    }

    public function alias(string $alias, string $class): void
    {
        $this->aliasLoader->set($alias, $class);
    }

    public function aliases(array $aliases): void
    {
        $this->aliasLoader->setAliases($aliases);
    }

    public function registerAliases(): void
    {
        $this->aliasLoader->register();
    }

    public function swap(string $abstract, object $instance): void
    {
        $this->binder->instance($abstract, $instance);
        $this->resolved[$abstract] = true;

        $this->fireResolvingCallbacksIfExists($abstract, $instance);
    }

    public function swapMany(array $instances): void
    {
        foreach ($instances as $abstract => $instance) {
            $this->swap($abstract, $instance);
        }
    }

    public function unswap(string $abstract): void
    {
        $this->binder->removeInstance($abstract);
        unset($this->resolved[$abstract]);
    }

    public function unswapMany(array $abstracts): void
    {
        foreach ($abstracts as $abstract) {
            $this->unswap($abstract);
        }
    }

    public function isSwapped(string $abstract): bool
    {
        return $this->binder->boundInstance($abstract);
    }

    public function flushSwapped(): void
    {
        $this->binder->clearInstances();
        $this->resolved = [];
    }

}