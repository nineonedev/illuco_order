<?php

namespace Framework\Boostrap;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;

class LoadAliases implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $file = config('path.bootstrap.aliases');

        if (!file_exists($file)) {
            return; 
        }

        $aliases = require $file;
        $app->aliases($aliases);
    }
}