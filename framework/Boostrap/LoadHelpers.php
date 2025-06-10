<?php

namespace Framework\Boostrap;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;

class LoadHelpers implements BootstrapperInterface 
{
    public function bootstrap(Application $app): void
    {
        $helperDirectory = $app->basePath() . '/framework/Support/Helpers';

        if (!is_dir($helperDirectory)) return; 
        
        foreach (glob($helperDirectory . DS. '*.php') as $file) {
            require_once $file;
        }
    }
}