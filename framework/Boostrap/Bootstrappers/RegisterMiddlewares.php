<?php

namespace Framework\Boostrap\Bootstrappers;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Configurations\MiddlewareConfigurator;
use Framework\Core\Application;

class RegisterMiddlewares implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        /** @var MiddlewareConfigurator $config */
        $config = $app->make(MiddlewareConfigurator::class);
        
        $middlewares = require_once base_path('bootstrap/middlewares.php') ?? [];
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