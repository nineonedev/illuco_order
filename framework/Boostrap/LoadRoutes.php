<?php

namespace Framework\Boostrap;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Configurations\RoutingConfigurator;
use Framework\Core\Application;

class LoadRoutes implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $app->make(RoutingConfigurator::class)->load();
    }
}