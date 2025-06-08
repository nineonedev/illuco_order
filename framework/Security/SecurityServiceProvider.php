<?php 

namespace Framework\Security;

use Framework\Core\Application;
use Framework\Core\ServiceProvider;
use Framework\Security\Auth\AuthManager;
use Framework\Security\Auth\Providers\InMemoryUserProvider;
use Framework\Security\Auth\SessionGuard;
use Framework\Security\Contracts\EncrypterInterface;
use Framework\Security\Contracts\GuardInterface;
use Framework\Security\Contracts\HasherInterface;
use Framework\Security\Contracts\SessionInterface;
use Framework\Security\Contracts\TokenManagerInterface;
use Framework\Security\Contracts\UserProviderInterface;
use Framework\Security\Cookie\CookieManager;
use Framework\Security\Csrf\TokenManager;
use Framework\Security\Encryption\Encrypter;
use Framework\Security\Encryption\StringCipher;
use Framework\Security\Hash\BcryptHasher;
use Framework\Security\Session\DatabaseStore;
use Framework\Security\Session\InMemoryStore;
use Framework\Security\Session\SessionManager;
use Framework\Security\Session\Store;

class SecurityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 해싱
        $bcrypter = new BcryptHasher();
        $this->app->instance(BcryptHasher::class, $bcrypter);
        $this->app->instance(HasherInterface::class, $bcrypter);
        
        // 암호화
        $this->app->singleton(EncrypterInterface::class, function(){
            return new Encrypter(env('APP_KEY', config('app.key', 'changeme-32-byte-key!@#$%12345678'))); 
        });
        
        $this->app->singleton(StringCipher::class, function(Application $app){
            return new StringCipher($app->make(EncrypterInterface::class));
        });

        // 세션
        $this->app->singleton(SessionManager::class, function(){
            $manager = new SessionManager();

            $manager->setDriver('php', new Store()); 
            $manager->setDriver('db', new DatabaseStore());
            $manager->setDriver('memory', new InMemoryStore());
            
            $manager->use('php');

            return $manager; 
        });


        $this->app->singleton(UserProviderInterface::class, function(){
            return new InMemoryUserProvider();
        });

        $this->app->singleton(SessionInterface::class, function(Application $app) {
            return $app->make(SessionManager::class)->driver(); 
        });
        
        // CSRF
        $this->app->singleton(TokenManagerInterface::class, function(Application $app){
            return new TokenManager($app->make(SessionInterface::class));
        });

        // 쿠키
        $this->app->singleton(CookieManager::class, function(){
            return new CookieManager(); 
        });

        // AuthManager
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
    }
}