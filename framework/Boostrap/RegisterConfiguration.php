<?php

namespace Framework\Boostrap;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;
use Framework\State\Config;

class RegisterConfiguration implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $configPath = $app->configPath();
        
        if (!is_dir($configPath)) { 
            die("The settings directory does not exist.");
        }

        $config = new Config($configPath);
        $app->instance(Config::class, $config);
    }
}
