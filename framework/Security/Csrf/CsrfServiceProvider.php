<?php 

namespace Framework\Security\Csrf;

use Framework\Core\Application;
use Framework\Core\ServiceProvider;
use Framework\Security\Session\Contracts\SessionInterface; 

class CsrfServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TokenManager::class, function(Application $app){
            return new TokenManager($app->make(SessionInterface::class));
        });

        $this->app->singleton(TokenManagerInterface::class, function(Application $app){
            return $app->make(TokenManager::class); 
        });
    }
}