<?php 

namespace Framework\Boostrap;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;
use Framework\State\Context;
use Framework\State\Store;

class RegisterStores implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        
        $stores = config('app.stores', []); 
        
        $context = new Context();

        foreach ($stores as $store) {
            $context->addStore($store, new Store());
        }

        $app->instance(Context::class, $context); 
    }
}