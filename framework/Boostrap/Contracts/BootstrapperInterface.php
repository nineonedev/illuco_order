<?php

namespace Framework\Boostrap\Contracts;

use Framework\Core\Application;

interface BootstrapperInterface {
    public function bootstrap(Application $app): void;
}