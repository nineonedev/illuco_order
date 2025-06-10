<?php

namespace Framework\Boostrap\Bootstrappers;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Configurations\MiddlewareConfigurator;
use Framework\Core\Application;

class LoadMiddlewares implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        /** @var MiddlewareConfigurator $config */
        $config = $app->make(MiddlewareConfigurator::class);
        
        $middlewares = require config('path.bootstrap.providers') ?? [];
        $priority = $middlewares['priority'] ?? [];
        $global = $middlewares['global'] ?? [];
        $group = $middlewares['group'] ?? [];

        $config->addPriorityMany($priority); 
        $config->addGlobalMany($global); 

        foreach ($group as $key => $middleware) {
            $config->addGroupMany($key, $middleware);
        }
    }
}