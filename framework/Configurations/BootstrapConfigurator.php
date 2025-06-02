<?php 

namespace Framework\Configurations;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;

class BootstrapConfigurator 
{    
    protected Application $app;

    /**
     * @var array<class-string<BootstrapperInterface>,bool>
     */
    protected array $bootstrapped = [];

    /**
     * @var array<class-string<BootstrapperInterface>>
     */
    protected array $bootstrappers = []; 

    public function __construct(Application $app)
    {
        $this->app = $app; 
    }

    /**
     * @param array<class-string<BootstrapperInterface>> $bootstrappers
     */
    public function addMany($bootstrappers): void
    {
        $this->bootstrappers = array_merge($this->bootstrappers, $bootstrappers);
    }

    /**
     * @param class-string<BootstrapperInterface> $bootstrapper
     */
    public function add(string $bootstrapper): void
    {
        $this->bootstrappers[] = $bootstrapper; 
    }

    public function isBootstrapped(string $bootstrapperClass): bool
    {
        return isset($this->bootstrapped[$bootstrapperClass]) 
            && $this->bootstrapped[$bootstrapperClass] === true;
    }

    public function bootstrap(string $bootstrapperClass): void
    {
        if ($this->isBootstrapped($bootstrapperClass)) {
            return;
        }

        $bootstrapper = new $bootstrapperClass();

        /** @var BootstrapperInterface $bootstrapper */
        $bootstrapper->bootstrap($this->app);

        $this->bootstrapped[$bootstrapperClass] = true; 
    }

    public function load(): void
    {
        foreach ($this->bootstrappers as $bootstrapperClass) {
            $this->bootstrap($bootstrapperClass);
        }
    }
}