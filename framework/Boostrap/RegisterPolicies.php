<?php

namespace Framework\Boostrap;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;

class RegisterPolicies implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $path = config('path.bootstrap.policies');;

        if (!file_exists($path)) {
            return;
        }

        $policies = require $path; 

        foreach ($policies as $model => $policy) {
            gate()->policy($model, $policy);
        }
    }
}