<?php

namespace Framework\Boostrap\Bootstrappers;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;
use Framework\State\Env;

class LoadEnvironment implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $env = new Env(base_path('.env')); 
        $app->instance(Env::class, $env);

        $app->setEnvironment($env->get('APP_ENV', 'production'));
    }
}