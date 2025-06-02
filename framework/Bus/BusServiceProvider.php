<?php

namespace Framework\Bus;

use Framework\Core\Application;
use Framework\Core\ServiceProvider;
use Framework\Security\Auth\Events\LoginAttemped;
use Framework\Security\Auth\Listeners\SendLoginNotificationListener;
use Framework\Security\Auth\Listeners\UpdateLastLoginTimestampLIstener;

class BusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ListenerResolver::class, function(){
            return new ListenerResolver();
        }); 

        // $this->app->singleton(QueueManager::class, function(){
        //     return new QueueManager();
        // });

        // $this->app->singleton(EventDispatcher::class, function(Application $app){
        //     return new EventDispatcher(
        //         $app->make(ListenerResolver::class),
        //         $app->make(QueueManager::class)
        //     );
        // });
    }

    public function boot(): void
    {
        // $dispatcher = $this->app->make(EventDispatcher::class); 

        // $eventProvider = new EventServiceProvider($dispatcher); 
        // $eventProvider->setListen([
        //     LoginAttemped::class => [
        //         SendLoginNotificationListener::class,
        //         UpdateLastLoginTimestampLIstener::class
        //     ],
        // ]);

        // $eventProvider->register();
    }
}