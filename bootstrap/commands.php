<?php

use Framework\Database\Commands\MakeMigrationCommand;
use Framework\Database\Commands\MigrationFreshCommand;
use Framework\Database\Commands\MigrationMigrateCommand;
use Framework\Database\Commands\MigrationResetCommand;
use Framework\Database\Commands\MigrationRollbackCommand;
use Framework\Database\Commands\MigrationStatusCommand;

return [
    MigrationFreshCommand::class,
    MigrationRollbackCommand::class,
    MigrationMigrateCommand::class,
    MakeMigrationCommand::class,
    MigrationResetCommand::class,
    MigrationStatusCommand::class,
];