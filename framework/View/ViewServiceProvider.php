<?php

namespace Framework\View;

use Framework\Core\Application;
use Framework\Core\Contracts\DeferredProviderInterface;
use Framework\Core\ServiceProvider;
use Framework\View\Contracts\ViewFinderInterface;

class ViewServiceProvider extends ServiceProvider implements DeferredProviderInterface
{
    public function register(): void
    {
        $this->app->singleton(ViewEngine::class, function(Application $app){
            return new ViewEngine(
                new ViewFinder($app->viewPath()),
                new SectionManager(),
                new ComponentManager(),
            );
        });
    }

    public function provides(): array
    {
        return [
            ViewFinder::class,
            ViewFinderInterface::class,
            ViewEngine::class
        ];
    }
}