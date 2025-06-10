<?php

namespace Framework\Boostrap\Bootstrappers;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Console\CommandRegistry;
use Framework\Core\Application;

class LoadCommands implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        /** @var CommandRegistry $registry */
        $registry = $app->make(CommandRegistry::class);
        
        $commands = require_once config('path.bootstrap.commands') ?? [];
        $registry->addMany($commands); 
    }
}