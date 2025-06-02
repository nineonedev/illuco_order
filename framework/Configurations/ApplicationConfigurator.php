<?php 

namespace Framework\Configurations;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;
use Framework\Core\ServiceProvider;
use Framework\Support\ExceptionHandler;

class ApplicationConfigurator 
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;

        $app->instance(ExceptionConfigurator::class, new ExceptionConfigurator());
        $app->instance(MiddlewareConfigurator::class, new MiddlewareConfigurator());
        $app->instance(RoutingConfigurator::class, new RoutingConfigurator());
        $app->instance(BootstrapConfigurator::class, new BootstrapConfigurator($this->app));
    }

    /**
     * @param array<string,mixed> $bindings
     * @return $this
     */
    public function withBindings(array $bindings)
    {
        foreach ($bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }

        return $this; 
    }   

    /**
     * @param array<string,mixed> $singletons
     * @return $this
     */
    public function withSingletons(array $singletons)
    {
        foreach ($singletons as $abstract => $concrete) {
            $this->app->singleton($abstract, $concrete);
        }

        return $this; 
    }

    public function withExceptions(callable $callback)
    {
        /** @var ExceptionConfigurator $configurator */
        $configurator = $this->app->make(ExceptionConfigurator::class);
        $callback($configurator);

        /** @var ExceptionHandler $handler */
        $handler = $this->app->make(ExceptionHandler::class); 
        $handler->setConfigurator($configurator); 
        
        return $this;
    }

    /**
     * @param array{web?:string,console?:string,health?:string} $routes
     * @return $this
     */
    public function withRouting(array $routes)
    {
        /** @var RoutingConfigurator $configurator */
        $configurator = $this->app->make(RoutingConfigurator::class);
        
        if (isset($routes['web'])) {
            $configurator->web($routes['web']);
        }

        if (isset($routes['console'])) {
            $configurator->console($routes['console']);
        }

        if (isset($routes['health'])) {
            $configurator->health($routes['health']);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function withMiddleware(callable $callback)
    {
        $configurator = $this->app->make(MiddlewareConfigurator::class); 
        $callback($configurator); 

        
        return $this; 
    }

    /**
     * @param array<class-string<BootstrapperInterface>> $bootstrappers
     * @return $this
     */
    public function withBootstrappers(array $bootstrappers)
    {
        /** @var BootstrapConfigurator $configurator */
        $configurator = $this->app->make(BootstrapConfigurator::class);
        $configurator->addMany($bootstrappers); 
        
        return $this;
    }

    /**
     * @param array<string,string> aliases
     * @return $this
     * @example [App => Framework\Core\Application::class]
     */
    public function withAliases(array $aliases)
    {
        $this->app->aliases($aliases); 

        return $this; 
    }

    /**
     * @param array<class-string<ServiceProvider>> $providers
     * @return $this
     */
    public function withProviders($providers)
    {
        $this->app->addManyPendingProviders($providers); 
        return $this;
    }

    public function create()
    {
        $this->app->make(BootstrapConfigurator::class)->load(); 
        $this->app->register();
        $this->app->boot();
        $this->app->registerAliases();
        $this->app->make(RoutingConfigurator::class)->load();
        
        return $this->app;
    }
}