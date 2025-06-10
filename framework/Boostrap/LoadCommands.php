<?php

namespace Framework\Boostrap;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Console\CommandRegistry;
use Framework\Core\Application;

class LoadCommands implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $path = config('path.bootstrap.commands');

        if(!file_exists($path)) return; 

        /** @var CommandRegistry $registry */
        $registry = $app->make(CommandRegistry::class);
        $commands = require_once $path ?? [];
        $registry->addMany($commands); 
    }
}