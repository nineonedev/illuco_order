<?php 

namespace Framework\Boostrap\Bootstrappers;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;

class RegisterAliases implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $app->aliases([
            
        ]);
    }
}