<?php

use Framework\Database\Commands\FreshCommand;
use Framework\Database\Commands\MakeMigrationCommand;
use Framework\Database\Commands\MigrateCommand;
use Framework\Database\Commands\RollbackCommand;

return [
    MigrateCommand::class,
    MakeMigrationCommand::class,
    RollbackCommand::class,
    FreshCommand::class,
];