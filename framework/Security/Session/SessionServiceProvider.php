<?php 

namespace Framework\Security\Session;

use Framework\Core\Application;
use Framework\Core\ServiceProvider;
use Framework\Security\Auth\Providers\UserEntityProvider;
use Framework\Security\Auth\Providers\UserProviderInterface;
use Framework\Security\Session\Contracts\SessionInterface;
use Framework\Security\Session\Stores\DatabaseStore;
use Framework\Security\Session\Stores\InMemoryStore;

class SessionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SessionManager::class, function() {
            $manager = new SessionManager();
            $manager->setDriver('db', new DatabaseStore());
            $manager->setDriver('php', new InMemoryStore()); 
            $manager->use('db');
            return $manager;
        });

        $this->app->singleton(UserProviderInterface::class, function(){
            return new UserEntityProvider();
        });

        $this->app->singleton(SessionInterface::class, function(Application $app) {
            return $app->make(SessionManager::class);
        });
    }

    public function boot(): void
    {
        $gcChance = (int) config('auth.session.gc_probability', 1);
        
        if (mt_rand(1, 100) <= $gcChance) {
            /** @var SessionManager */
            $store = $this->app->make(SessionManager::class)->driver();
            $store->gc();
        }
    }
}