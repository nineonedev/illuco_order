<?php

namespace Framework\Security\Auth;

use Framework\Core\Application;
use Framework\Core\ServiceProvider;
use Framework\Security\Auth\Contracts\GateInterface;
use Framework\Security\Auth\Contracts\GuardInterface;
use Framework\Security\Auth\Providers\UserProviderInterface;
use Framework\Security\Session\Contracts\SessionInterface; 

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuthManager::class, function(Application $app){
            $manager = new AuthManager();
            $provider = $app->make(UserProviderInterface::class);
            $session = $app->make(SessionInterface::class); 

            $manager->setGuard('web', new SessionGuard($provider, $session));
            $manager->use('web'); 
            
            return $manager;
        });

        $this->app->bind(GuardInterface::class, function(Application $app){
            return $app->make(AuthManager::class)->guard();
        });

        $this->app->singleton(GateInterface::class, function () {
            return new GateManager(); 
        });
    }
}