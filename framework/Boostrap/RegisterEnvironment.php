<?php

namespace Framework\Boostrap;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;
use Framework\State\Env;

class RegisterEnvironment implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $file = $app->basePath().'/.env';
        
        if (!file_exists($file)) {
            die('The .env file does not exist.');
        }

        $env = new Env($file); 
        $app->instance(Env::class, $env);
        $app->setEnvironment($env->get('APP_ENV', 'production'));
    }
}