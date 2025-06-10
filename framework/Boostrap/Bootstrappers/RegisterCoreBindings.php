<?php

namespace Framework\Boostrap\Bootstrappers;

use Framework\Configurations\ExceptionConfigurator;
use Framework\Configurations\MiddlewareConfigurator;
use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Console\CommandRegistry;
use Framework\Core\Contracts\KernelInterface;
use Framework\Core\Application;
use Framework\Http\Kernel as HttpKernel;
use Framework\Console\Kernel as ConsoleKernel;
use Framework\Console\Lock\LockManager;
use Framework\Console\Output\Output;
use Framework\Routing\Contracts\RouterInterface;
use Framework\Routing\Router;
use Framework\Support\Exceptions\ExceptionHandler;
use Framework\View\ComponentManager;
use Framework\View\Contracts\ViewFinderInterface;
use Framework\View\SectionManager;
use Framework\View\ViewFinder;
use Framework\View\ViewRenderer;

class RegisterCoreBindings implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        // Routing & Kernel
        $router = new Router();
        $app->singleton(Router::class, fn () => $router);
        $app->singleton(RouterInterface::class, fn () => $router);
        $app->instance(HttpKernel::class, new HttpKernel($app, $router, $app->make(MiddlewareConfigurator::class)));

        // Commands
        $app->singleton(CommandRegistry::class, fn() => new CommandRegistry());
        $app->singleton(LockManager::class, fn() => new LockManager());
        $app->singleton(Output::class, fn () => new Output());

        // Console Kernel
        $app->singleton(ConsoleKernel::class, function (Application $app)  {
            return new ConsoleKernel(
                $app->make(CommandRegistry::class),
                $app->make(Output::class),
                $app->make(LockManager::class)
            );
        });

        // Set Kernel
        $kernelClass = php_sapi_name() === 'cli'
            ? ConsoleKernel::class
            : HttpKernel::class;
        $app->singleton(KernelInterface::class, $kernelClass);
        $app->alias(KernelInterface::class, 'kernel');
    }
}
