<?php

namespace App\Services\Auth;

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