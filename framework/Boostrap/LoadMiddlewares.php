<?php 

namespace Framework\Boostrap;

use Exception;
use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Configurations\MiddlewareConfigurator;
use Framework\Core\Application;

class LoadMiddlewares implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        /** @var MiddlewareConfigurator $configurator */
        $configurator = app(MiddlewareConfigurator::class);

        $middlewares = require config('path.bootstrap.middlewares') ?? [];
        $globals = $middlewares['global'] ?? [];
        $groups = $middlewares['group'] ?? [];
        $priorities = $middlewares['priority'] ?? [];

        // register configured middlewares

        $configurator->addGlobalMany($globals); 
        $configurator->addPriorityMany($priorities);

        foreach ($groups as $group => $middlewares) {
            $configurator->addGroupMany($group, $middlewares);
        }
    }
}