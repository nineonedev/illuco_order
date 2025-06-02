<?php

namespace Framework\Database;

use Framework\Core\Application;
use Framework\Core\ServiceProvider;
use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Migrations\MigrationRepository;
use Framework\Database\Migrations\MigrationRunner;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // DatabaseManager 등록
        $this->app->singleton(DatabaseManager::class, function () {
            $config = config('database.connections');
            $manager = new DatabaseManager($config);

            $manager->setDefaultConnection(config('database.default'));

            return $manager;
        });

        // Database 클래스 등록
        $this->app->singleton(Database::class, function ($app) {
            return new Database($app->make(DatabaseManager::class));
        });

        // 기본 Connection 바인딩
        $this->app->bind(ConnectionInterface::class, function ($app) {
            return $app->make(DatabaseManager::class)->connection();
        });

        // alias('db', ConnectionInterface::class) 바인딩
        $this->app->alias(ConnectionInterface::class, 'db');

        // MigrationRepository 등록
        $this->app->singleton(MigrationRepository::class, function (Application $app) {
            return new MigrationRepository($app->make(ConnectionInterface::class));
        });

        // MigrationRunner 등록
        $this->app->singleton(MigrationRunner::class, function (Application $app) {
            return new MigrationRunner(
                $app->make(MigrationRepository::class),
                $app->make(ConnectionInterface::class),
                config('database.migrations.path', 'database/migrations')
            );
        });
    }
}
