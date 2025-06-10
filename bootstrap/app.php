<?php

use Framework\Configurations\ExceptionConfigurator;
use Framework\Configurations\MiddlewareConfigurator;
use Framework\Core\Application;

define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(__DIR__));
define('BASE_AUTOLOAD_FILE', BASE_PATH.'/vendor/autoload.php');

if (!file_exists(BASE_AUTOLOAD_FILE)) {
    die('Cannot Applicatoin Booting with autoload file.');
}

require_once BASE_AUTOLOAD_FILE;

return Application::configure(BASE_PATH)
    ->withProviders(require BASE_PATH.'/bootstrap/providers.php')
    ->withBootstrappers(require BASE_PATH.'/bootstrap/bootstrappers.php')
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
    