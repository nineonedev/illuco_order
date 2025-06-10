<?php

namespace Framework\Boostrap;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;

class LoadRelations implements BootstrapperInterface 
{
    public function bootstrap(Application $app): void
    {
        $file = config('path.bootstrap.relations');
        
        if (file_exists($file)) {
            require_once $file; 
        }
    }
}