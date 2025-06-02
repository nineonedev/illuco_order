<?php

namespace Framework\View;

use Framework\Core\ServiceProvider;

class ViewServiceProvider extends ServiceProvider 
{
    public function register(): void
    {
        $this->app->singleton(ViewRenderer::class, function(){
            return new ViewRenderer(
                new ViewFinder(config('path.view')),
                new SectionManager(),
                new ComponentManager(),
            );
        });
    }
}