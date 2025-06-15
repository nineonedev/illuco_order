<?php 

use Framework\Boostrap\LoadCommands;
use Framework\Boostrap\LoadHelpers;
use Framework\Boostrap\LoadRelations;
use Framework\Boostrap\LoadRoutes;
use Framework\Boostrap\RegisterConfiguration;
use Framework\Boostrap\RegisterCoreBindings;
use Framework\Boostrap\RegisterEnvironment;
use Framework\Boostrap\RegisterTranslators;
use Framework\Boostrap\HandleExceptions;
use Framework\Boostrap\LoadAliases;
use Framework\Boostrap\LoadMiddlewares;
use Framework\Boostrap\RegisterStores;

return [
    HandleExceptions::class,
    LoadHelpers::class,
    RegisterConfiguration::class,
    RegisterEnvironment::class,
    RegisterStores::class,
    RegisterCoreBindings::class,
    RegisterTranslators::class,
    LoadAliases::class,
    LoadMiddlewares::class,
    LoadCommands::class,
    LoadRoutes::class,
    LoadRelations::class,
];
