<?php

namespace App\Services\Common;

use App\Domains\Common\Entities\Role;
use App\Domains\Common\Repositories\PermissionRepository;
use App\Domains\Common\Repositories\RoleRepository;
use App\Supports\Services\Service;
use Framework\Support\Collection;
use RuntimeException;

class SaveRoleService extends Service
{
    protected function handle(array $payload): array
    {
        $role = $payload['role'] ?? [];
        $permissionInputs = $payload['permissions'] ?? []; 

        $role = new Role($role);
        $saved = RoleRepository::make()->save($role);

        if (!$saved && !$role->getPrimaryKey()) {
            throw new RuntimeException('역할 저장 실패');
        }

        $role = $saved ?: $role;
        
        $permissions = $this->parsePermissionInputs($permissionInputs);
        $permissionIds = $this->resolvePermissionIds($permissions);
        
        $role = RoleRepository::with(['permissions'])->find($role->getPrimaryKey());
        $role->permissions()->sync($permissionIds);

        return ['role' => $role->toArray()];
    }

    protected function parsePermissionInputs(array $permissionInputs): array
    {
        $permissions = [];

        foreach ($permissionInputs as $resource => $actions) {
            foreach ($actions as $action) {
                $permissions[] = [
                    'resource' => $resource, 
                    'action' => $action,
                ];
            }
        }

        return $permissions;
    }

    /**
     * @param array $permissions [['resource' => '...', 'action' => '...'], ...]
     */
    protected function resolvePermissionIds(array $permissions): array
    {
        $ids = [];

        foreach ($permissions as $permission) {
            if (!isset($permission['resource'], $permission['action'])) continue;

            $found = PermissionRepository::where('resource', $permission['resource'])
                ->where('action', $permission['action'])
                ->first();

            if ($found) {
                $ids[$found->id] = $found;
            }
        }

        return $ids;
    }
}