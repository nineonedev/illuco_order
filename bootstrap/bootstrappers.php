<?php 

use Framework\Boostrap\Bootstrappers\LoadHelpers;
use Framework\Boostrap\Bootstrappers\RegisterCoreBindings;
use Framework\Boostrap\Bootstrappers\RegisterAliases;
use Framework\Boostrap\Bootstrappers\LoadStates;
use Framework\Boostrap\Bootstrappers\LoadEnvironment;
use Framework\Boostrap\Bootstrappers\RegisterCommands;
use Framework\Boostrap\Bootstrappers\RegisterMiddlewares;
use Framework\Boostrap\Bootstrappers\RegisterRelations;
use Framework\Boostrap\Bootstrappers\SetExceptionHandler;

return [
    LoadHelpers::class,
    RegisterCoreBindings::class,
    RegisterAliases::class,
    LoadStates::class,
    LoadEnvironment::class,
    SetExceptionHandler::class,
    RegisterMiddlewares::class,
    RegisterCommands::class,
    RegisterRelations::class,
];