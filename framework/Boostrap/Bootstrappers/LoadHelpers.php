<?php 

namespace Framework\Boostrap\Bootstrappers;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;

class LoadHelpers implements BootstrapperInterface 
{
    public function bootstrap(Application $app): void
    {
        $helperDirectory = BASE_PATH.'/framework/Support/Helpers';
        
        foreach (glob($helperDirectory . DS. '*.php') as $file) {
            require_once $file;
        }
    }
}