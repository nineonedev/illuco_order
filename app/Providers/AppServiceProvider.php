<?php

namespace App\Providers;

use App\Domains\Auth\Repositories\PermissionRepository;
use Framework\Core\ServiceProvider;
use Framework\Database\ORM\PermissionMap;
use Framework\Security\Auth\Access\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 이곳에 별다른 설정은 없을 것입니다.
    }

    public function boot(): void
    {
        $this->handlePermissions();
    }

    protected function handlePermissions(): void
    {
        // 권한이 활성화되어 있는지 체크
        if (!config('database.features.register_permissions', false)) {
            return; 
        }

        // 권한 설정 파일 경로
        $file = config('path.bootstrap.permissions');
        
        // 권한 설정 파일이 없으면 처리하지 않음
        if (!file_exists($file)) {
            return;
        }

        // 데이터베이스에 권한 테이블이 있는지 체크
        if (!db()->schema()->hasTable(PermissionRepository::table())) {
            return; 
        }

        // 권한 설정을 파일로부터 불러옵니다.
        $permissions = require_once $file;

        // PermissionMap 설정
        PermissionMap::config($permissions);
        PermissionMap::handle();  // 권한 테이블에 기록

        context()->set('permissions', $permissions);
        context()->set('permission_actions', PermissionMap::$allowedActions);

        // Gate에서 각 권한을 정의
        $this->definePermissions();
    }

    protected function definePermissions(): void
    {
        if (!user()) {
            return;
        }

         // 사용자 역할을 기반으로 권한 정의
        foreach (user()->roles as $role) {
            foreach ($role->permissions as $permission) {
                // Gate 정의: 'resource.action'을 권한으로 설정
                Gate::define("{$permission->resource}.{$permission->action}", function ($user) use ($permission) {
                    // 사용자가 해당 권한을 가지고 있는지 확인
                    return $this->checkPermission($user, $permission->resource, $permission->action);
                });

                // 디버그용: 권한 출력
                // dump("Defined permission: {$permission->resource}.{$permission->action}");
            }
        }

        // PermissionMap에 정의된 권한들을 반복문으로 처리
        // foreach (PermissionMap::$map as $entityClass => $actions) {
        //     $alias = $entityClass::alias();  // 예: 'order', 'cart' 등

        //     foreach ($actions as $action) {
        //         // Gate에 권한을 정의
        //         Gate::define("{$alias}.{$action}", function ($user) use ($alias, $action) {
        //             // 사용자가 해당 권한을 가지고 있는지 확인
        //             return $this->checkPermission($user, $alias, $action);
        //         });
        //     }
        // }
    }

    protected function checkPermission($user, $resource, $action): bool
    {
        // 사용자 역할을 가져옵니다.
        $roles = $user->roles;

        // 각 역할의 권한을 수동으로 추출하여 'resource.action' 형식으로 비교합니다.
        $rolePermissions = [];

        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                // 'resource.action' 형식으로 권한을 저장
                $rolePermissions[] = "{$permission->resource}.{$permission->action}";
            }
        }

        // 해당 role에 권한이 있는지 체크
        return in_array("{$resource}.{$action}", $rolePermissions);
    }

}
