<?php 

namespace Framework\Core;

use Framework\Configurations\ApplicationConfigurator;
use Framework\Console\Input\Input;
use Framework\Core\Contracts\ApplicationInterface;
use Framework\Core\Contracts\DeferredProviderInterface;
use Framework\Http\Request;
use Framework\Console\Kernel as ConsoleKernel;
use Framework\Http\Kernel as HttpKernel;

class Application extends Container implements ApplicationInterface
{
    public static ?string $BASE_PATH = null;
    protected static ?Application $instance = null;

    protected string $basePath; 

    protected string $environment = 'production'; 

    protected bool $isBooted = false;

    protected array $bootingCallbacks = []; 

    protected array $bootedCallbacks = [];

    protected bool $registered = false; 
    
    /**
     * @var array<class-string<ServiceProvider>>,bool>
     */
    protected array $bootedProviders = [];

    /**
     * @var ServiceProvider[]
     */
    protected array $providers = []; 

    /**
     * @var array<class-string<ServiceProvider>|ServiceProvider>
     */
    protected array $pendingProviders = []; 

    /**
     * @var array<string,string>
     */
    protected array $deferredServices = [];
    
    /**
     * @var array<class-string<ServiceProvider>,bool>
     */
    protected array $loadedProviders = [];


    public static function configure(string $basePath)
    {
        return new ApplicationConfigurator(new Application($basePath)); 
    }

    public function __construct(?string $basePath = null)
    {
        parent::__construct(); 
        
        $this->basePath = $basePath;
        static::$BASE_PATH = $basePath;

        $this->instance(Application::class, $this);
        $this->instance(ApplicationInterface::class, $this);
        static::setInstance($this); 
    }

    public static function setInstance(Application $app)
    {
        static::$instance = $app;
    }

    public static function getInstance(?string $basePath = null): ?Application
    {
        if (static::$instance) {
            return static::$instance;
        }

        static::$instance = new Application($basePath); 
        return static::$instance; 
    }
    
    public function basePath(): string
    {
        return $this->basePath;
    }

    public function storagePath(): string
    {
        return $this->basePath . '/storage';
    }

    public function configPath(): string
    {
        return $this->basePath . '/config';
    }

    public function viewPath(): string
    {
        return $this->basePath . '/resources/views';
    }

    public function handleCommand(Input $input): void
    {
        /** @var ConsoleKernel $kernel */
        $kernel = $this->make(ConsoleKernel::class); 
        $kernel->handle($input);
        $kernel->terminate($input); 
    }
    
    public function handleRequest(Request $request): void
    {
        /** @var HttpKernel $kernel */
        $kernel = $this->make(HttpKernel::class);
        $response = $kernel->handle($request)->send();
        $kernel->terminate($request, $response);
    }

    public function isDevelopment(): bool
    {
        return !$this->isProduction();
    }

    public function isProduction(): bool
    {
        return $this->environment === 'production'; 
    }

    public function setEnvironment(string $env): void
    {
        $this->environment = $env;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @return mixed
     */
    public function make(string $abstract, array $parameters = [])
    {
        $this->loadDeferredProviderIfNeeded($abstract); 
        return parent::make($abstract, $parameters); 
    }

    /**
     * @var class-string<ServiceProvider|ServiceProvider> $provider
     */
    public function addPendingProvider($provider): void
    {
        if (array_search($provider, $this->pendingProviders, true)) {
            return; 
        }

        $this->pendingProviders[] = $provider;
    }

    /**
     * @param array<class-string<ServiceProvider>|ServiceProvider> $providers
     */
    public function addManyPendingProviders(array $providers): void
    {
        foreach ($providers as $provider) {
            $this->addPendingProvider($provider); 
        }
    }

    public function register(): void
    {
        if ($this->registered) return; 

        foreach ($this->pendingProviders as $provider) {
            $this->registerProvider($provider); 
        }
        
        $this->pendingProviders = [];
        $this->registered = true;
    }

    /**
     * @param class-string<ServiceProvider>|ServiceProvider $provider
     */
    protected function registerProvider($provider): void
    {
        if (is_string($provider)) {
            $provider = new $provider($this);
        };

        $className = get_class($provider);

        if ( is_subclass_of($provider, DeferredProviderInterface::class)) {
            /** @var DeferredProviderInterface $provider */

            foreach ($provider->provides() as $abstract) {
                $this->deferredServices[$abstract] = $className; 
            }
            
            return; 
        }

        $this->loadProvider($provider); 
    }

    protected function forgetDeferredServicesOf(string $providerClass): void
    {
        foreach ($this->deferredServices as $abstract => $provider) {
            if ($provider === $providerClass) {
                unset($this->deferredServices[$abstract]);
            }
        }
    }

    protected function loadProvider(ServiceProvider $provider)
    {
        if ($this->providerIsLoaded(get_class($provider))) {
            return; 
        }
        
        $provider->register(); 
        $this->providers[] = $provider; 
        $this->loadedProviders[get_class($provider)] = true;
    }

    /**
     * @var class-string<ServiceProvider> $provider
     */
    public function providerIsLoaded(string $provider): bool
    {
        return isset($this->loadedProviders[$provider]); 
    }

    public function loadDeferredProviderIfNeeded(string $abstract): void
    {
        if (!isset($this->deferredServices[$abstract])) {
            return; 
        }

        $providerClass = $this->deferredServices[$abstract]; 

        if ($this->providerIsLoaded($providerClass)) {
            return; 
        }

        $provider = new $providerClass($this);

        $this->loadProvider($provider); 
        $this->bootProvider($provider); 
        $this->forgetDeferredServicesOf($providerClass);
    }   

    protected function bootProvider(ServiceProvider $provider)
    {
        $providerClass = get_class($provider);

        if (isset($this->bootedProviders[$providerClass]) 
        && $this->bootedProviders[$providerClass]) {
            return;
        }
        
        if (method_exists($provider, 'boot')) {
            $provider->boot();
        }

        $this->bootedProviders[$providerClass] = true;  
    }

    public function boot(): void
    {
        if ($this->isBooted) {
            return; 
        }

        $this->fireCallbacks($this->bootingCallbacks);

        foreach ($this->providers as $provider) {
            $this->bootProvider($provider); 
        }

        $this->isBooted = true;

        $this->fireCallbacks($this->bootedCallbacks);
    }

    public function booting(callable $callback): void
    {
        $this->bootingCallbacks[] = $callback;
    }

    public function booted(callable $callback): void
    {
        $this->bootedCallbacks[] = $callback;
    }

    public function isBooted(): bool
    {
        return $this->isBooted; 
    }

    protected function fireCallbacks(array $callbacks): void
    {
        foreach ($callbacks as $callback) {
            $callback($this); 
        }
    }
}