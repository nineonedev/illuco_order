<?php

namespace Framework\Database;

use Framework\Core\Application;
use Framework\Core\ServiceProvider;
use Framework\Database\Contracts\ConnectionInterface;
use Framework\Database\Migration\MigrationRepository;
use Framework\Database\Migration\Migrator;
use Framework\Database\Schema\Schema;
use Framework\Support\Test;

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
        $this->app->singleton(ConnectionInterface::class, function ($app) {
            return $app->make(DatabaseManager::class)->connection();
        });
        
        $this->app->singleton(Schema::class, function(Application $app){
            return new Schema($app->make(ConnectionInterface::class));
        });

        // MigrationRepository 등록
        $this->app->singleton(MigrationRepository::class, function (Application $app) {
            return new MigrationRepository($app->make(ConnectionInterface::class));
        });

        // MigrationRunner 등록
        $this->app->singleton(Migrator::class, function (Application $app) {
            return new Migrator(
                $app->make(MigrationRepository::class),
                $app->make(ConnectionInterface::class),
                config('database.migrations.path', 'database/migrations')
            );
        });
    }
}
