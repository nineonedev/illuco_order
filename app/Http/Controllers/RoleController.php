<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Repositories\RoleRepository;
use App\Http\Requests\Auth\SaveRoleRequest;
use App\Services\Auth\SaveRoleService;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page') ?? 1;
        $roles = RoleRepository::queryStatic()->paginate(15, $page);

        return $this->render('admin.pages.roles.index', [
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        return $this->render('admin.pages.roles.create');
    }

    public function edit(string $id)
    {
        $role = RoleRepository::make()->with(['permissions'])->query()->find($id);

        if (!$role) {
            return $this->renderError(null, "정보를 찾을 수 없습니다.");
        }

        return $this->render('admin.pages.roles.edit', ['role' => $role]);
    }

    public function store(SaveRoleRequest $request)
    {
        $data = $request->only(['name', 'description']);
        $permissionInputs = $request->input('permissions', []);

        $result = (new SaveRoleService())->runInTransaction([
            'role' => $data,
            'permissions' => $permissionInputs,
        ]);

        return $result->toResponse();
    }

    public function update(string $id, SaveRoleRequest $request)
    {
        $role = RoleRepository::queryStatic()->find($id);

        if (!$role) {
            return $this->renderError(null, "정보를 찾을 수 없습니다.");
        }

        $request->merge(['id' => $id]);
        $data = $request->only(['name', 'description']);
        $permissionInputs = $request->body('permissions', []);

        $result = (new SaveRoleService())->runInTransaction([
            'role' => $role->fill($data),
            'permissions' => $permissionInputs,
        ]);

        return $result->toResponse();
    }

    public function destroy(string $id)
    {
        return $this->runInTransaction(function () use ($id) {
            $role = RoleRepository::queryStatic()->find($id);
            
            if (!$role) {
                throw new RuntimeException("정보를 찾을 수 없습니다.");
            }

            if (!RoleRepository::make()->delete($role)) {
                throw new RuntimeException("삭제에 실패하였습니다.");
            }

            return $this->responseWith()
                ->success(true)
                ->message('삭제되었습니다.')
                ->back()
                ->send();
        });
    }
}
