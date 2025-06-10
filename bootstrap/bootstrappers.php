<?php 

use Framework\Boostrap\Bootstrappers\LoadCommands;
use Framework\Boostrap\Bootstrappers\LoadHelpers;
use Framework\Boostrap\Bootstrappers\LoadMiddlewares;
use Framework\Boostrap\Bootstrappers\LoadRelations;
use Framework\Boostrap\Bootstrappers\LoadRoutes;
use Framework\Boostrap\Bootstrappers\RegisterConfiguration;
use Framework\Boostrap\Bootstrappers\RegisterCoreBindings;
use Framework\Boostrap\Bootstrappers\RegisterEnvironment;
use Framework\Boostrap\Bootstrappers\RegisterTranslators;
use Framework\Boostrap\Bootstrappers\HandleExceptions;

return [
    HandleExceptions::class,
    LoadHelpers::class,
    RegisterEnvironment::class,
    RegisterConfiguration::class,
    RegisterCoreBindings::class,
    RegisterTranslators::class,
    LoadMiddlewares::class,
    LoadCommands::class,
    LoadRelations::class,
    LoadRoutes::class,
];
