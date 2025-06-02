<?php

namespace Framework\Boostrap\Bootstrappers;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Console\CommandCollection;
use Framework\Core\Application;

class RegisterCommands implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $collection = $app->make(CommandCollection::class);
        
        $commands = require_once base_path('bootstrap/commands.php') ?? [];
        $collection->addMany($commands); 
    }
}