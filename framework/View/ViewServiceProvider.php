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
        $this->app->singleton(ViewFinder::class, function (Application $app) {
            return new ViewFinder(config('path.view'));
        });

        $this->app->singleton(ViewFinderInterface::class, function (Application $app) {
            return $app->make(ViewFinder::class); 
        });
        
        $this->app->singleton(ViewRenderer::class, function(Application $app){
            return new ViewRenderer(
                $app->make(ViewFinderInterface::class),
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
            ViewRenderer::class
        ];
    }
}