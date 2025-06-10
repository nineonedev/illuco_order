<?php 

namespace Framework\Configurations;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;
use Framework\Core\ServiceProvider;
use Framework\Support\Exceptions\ExceptionHandler;

class ApplicationConfigurator 
{
    protected Application $app;
    protected MiddlewareConfigurator $middleware;
    protected RoutingConfigurator $routing; 
    protected ExceptionConfigurator $exception;
    protected BootstrapConfigurator $bootstrap;
    

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->exception = new ExceptionConfigurator($this->app);
        $this->middleware = new MiddlewareConfigurator($this->app);
        $this->routing = new RoutingConfigurator($this->app); 
        $this->bootstrap = new BootstrapConfigurator($this->app);

        $this->app->instance(BootstrapConfigurator::class, $this->bootstrap);
        $this->app->instance(MiddlewareConfigurator::class, $this->middleware);
        $this->app->instance(ExceptionConfigurator::class, $this->exception);
        $this->app->instance(RoutingConfigurator::class, $this->routing);
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
        $callback($this->exception);

        /** @var ExceptionHandler $handler */
        $handler = $this->app->make(ExceptionHandler::class); 
        $handler->setConfigurator($this->exception); 
        
        return $this;
    }

    /**
     * @param array{web?:string,console?:string,health?:string} $routes
     * @return $this
     */
    public function withRouting(array $routes)
    {
        if (isset($routes['web'])) {
            $this->routing->web($routes['web']);
        }

        if (isset($routes['console'])) {
            $this->routing->console($routes['console']);
        }

        if (isset($routes['health'])) {
            $this->routing->health($routes['health']);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function withMiddleware(callable $callback)
    {
        $callback($this->middleware); 
        return $this; 
    }

    /**
     * @param array<class-string<BootstrapperInterface>> $bootstrappers
     * @return $this
     */
    public function withBootstrappers(array $bootstrappers)
    {
        $this->bootstrap->addMany($bootstrappers); 
        
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
        $this->app->registerAliases();
        $this->bootstrap->load();
        $this->app->register();
        $this->app->boot();
        $this->exception->setDebug(env('APP_DEBUG', config('app.debug', false)));
        
        return $this->app;
    }
}