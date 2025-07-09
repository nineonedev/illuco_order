<?php

namespace App\Services\Auth;

use App\Domains\Auth\Entities\Permission;
use App\Domains\Auth\Entities\Role;
use App\Domains\Auth\Repositories\PermissionRepository;
use App\Domains\Auth\Repositories\RoleRepository;
use App\Supports\Services\Service;
use Framework\Database\ORM\Entities\Entity;
use RuntimeException;

class SaveRoleService extends Service
{
    protected function handle(array $payload): array
    {
        $role = $payload['role'] ?? [];
        $permissionInputs = $payload['permissions'] ?? [];

        $role = $role instanceof Entity ? $role : new Role($role);
        $role = RoleRepository::make()->save($role);

        if (!$role) {
            throw new RuntimeException('역할 저장 실패');
        }

        $permissions = $this->parsePermissionInputs($permissionInputs);
        $permissionIds = $this->resolvePermissionIds($permissions);

        $role->permissions()->sync($permissionIds);

        return ['role' => $role->toArray()];
    }

    protected function parsePermissionInputs(array $permissionInputs): array
    {
        $permissions = [];

        foreach ($permissionInputs as $resource => $actions) {
            $resource = is_string($resource) && class_exists($resource)
                ? $resource::alias()
                : $resource;

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
     * @return array<int> permission IDs
     */
    protected function resolvePermissionIds(array $permissions): array
    {
        $ids = [];

        foreach ($permissions as $permission) {
            $resource = $permission['resource'] ?? null;
            $action = $permission['action'] ?? null;

            if (!$resource || !$action) continue;

            $found = PermissionRepository::queryStatic()
                ->where('resource', $resource)
                ->where('action', $action)
                ->first();

            if (!$found) {
                throw new RuntimeException("Permission not found: {$resource}.{$action}");
            }

            $ids[] = $found->id;
        }

        return $ids;
    }
}
