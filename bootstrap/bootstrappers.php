<?php

use Framework\Boostrap\SetLocalePrefix;
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
use Framework\Boostrap\RegisterPolicies;
use Framework\Boostrap\RegisterStores;

return [
    HandleExceptions::class,
    LoadHelpers::class,
    RegisterConfiguration::class,
    RegisterEnvironment::class,
    RegisterStores::class,
    RegisterTranslators::class,
    SetLocalePrefix::class,
    RegisterCoreBindings::class,
    LoadAliases::class,
    LoadMiddlewares::class,
    LoadCommands::class,
    LoadRoutes::class,
    LoadRelations::class,
    RegisterPolicies::class,
];
