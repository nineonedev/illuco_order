<?php

use Framework\Configurations\ExceptionConfigurator;
use Framework\Configurations\MiddlewareConfigurator;
use Framework\Core\Application;

define('BASE_PATH', dirname(__DIR__));
define('DS', DIRECTORY_SEPARATOR);

// spl_autoload_register(function ($class) {
//     $path = BASE_PATH . '/' . lcfirst($class) . '.php';
//     $file = str_replace('\\', '/', $path);

//     if (file_exists($file)) {
//         require $file;
//     }
// });

if (file_exists(BASE_PATH.'/vendor/autoload.php')) {
    require_once BASE_PATH.'/vendor/autoload.php';
}

return Application::configure(BASE_PATH)
    ->withBootstrappers(require_once BASE_PATH.'/bootstrap/bootstrappers.php')
    ->withProviders(require_once BASE_PATH.'/bootstrap/providers.php')
    ->withRouting([
        'web' => BASE_PATH.'/routes/web.php',
        'console' => BASE_PATH.'/routes/console.php',
        'health' => BASE_PATH.'/routes/health.php'
    ])
    ->withMiddleware(function(MiddlewareConfigurator $middleware){
        
    })
    ->withExceptions(function(ExceptionConfigurator $exceptions){

    })
    ->create();
    