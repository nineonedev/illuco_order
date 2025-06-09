<?php

namespace Framework\View;

use Framework\Core\Application;
use Framework\Core\ServiceProvider;

class ViewServiceProvider extends ServiceProvider 
{
    public function register(): void
    {
        $this->app->singleton(ViewFinderInterface::class, function (Application $app) {
            return new ViewFinder(config('path.view'));
        });
        
        $this->app->singleton(ViewRenderer::class, function(Application $app){
            return new ViewRenderer(
                $app->make(ViewFinderInterface::class),
                new SectionManager(),
                new ComponentManager(),
            );
        });
    }
}