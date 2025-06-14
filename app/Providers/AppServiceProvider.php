<?php

namespace App\Providers;

use App\Domains\Common\Repositories\PermissionRepository;
use Framework\Core\ServiceProvider;
use Framework\Database\ORM\PermissionMap;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
    }

    public function boot(): void
    {
        $this->handlePermissions();
    }

    protected function handlePermissions(): void
    {
        if (!config('database.features.register_permissions', false)) {
            return; 
        }

        $file = config('path.bootstrap.permissions');
        
        if (!file_exists($file)) {
            return;
        }

        if (!db()->schema()->hasTable(PermissionRepository::table())) {
            return; 
        }

        $permissions = require_once $file;
        PermissionMap::config($permissions);
        PermissionMap::handle();
    }
}