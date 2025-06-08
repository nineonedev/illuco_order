<?php 

namespace Framework\Boostrap\Bootstrappers;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;

class RegisterRelations implements BootstrapperInterface 
{
    public function bootstrap(Application $app): void
    {
        require_once base_path('bootstrap/relations.php');
    }
}