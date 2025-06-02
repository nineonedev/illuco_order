<?php

namespace Framework\Boostrap\Bootstrappers;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Console\CommandRegistry;
use Framework\Core\Application;

class RegisterCommands implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        /** @var CommandRegistry $registry */
        $registry = $app->make(CommandRegistry::class);
        
        $commands = require_once base_path('bootstrap/commands.php') ?? [];
        $registry->addMany($commands); 
    }
}