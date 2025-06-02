<?php

namespace Framework\Boostrap\Bootstrappers;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;
use Framework\State\Config;
use Framework\State\Env;

class LoadStates implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $app->instance(Config::class, new Config(base_path('config')));
    }
}