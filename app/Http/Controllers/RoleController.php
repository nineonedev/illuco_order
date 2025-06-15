<?php

namespace App\Http\Controllers;

use App\Domains\Common\Repositories\RoleRepository;
use App\Http\Requests\Common\SaveRoleRequest;
use App\Services\Common\SaveRoleService;
use Framework\Http\Request;
use Framework\Routing\Controller;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page') ?? 1;
        $roles = RoleRepository::paginate(15, $page);

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
        if (!$id) {
            return $this->renderError(null, "정보를 찾을 수 없습니다.");
        }

        $role = RoleRepository::with(['permissions'])->find($id);
        return $this->render('admin.pages.roles.edit', ['role' => $role]);
    }

    public function update(string $id, SaveRoleRequest $request)
    {
        if (!$id) {
            return $this->renderError(null, "정보를 찾을 수 없습니다.");
        }

        $request->merge(['id' => $id]);
        $data = $request->only(['name', 'description', 'id']);
        $permissionInputs = $request->body('permissions', []);

        $result = (new SaveRoleService())->runInTransaction([
            'role' => $data,
            'permissions' => $permissionInputs,
        ]);

        return $result->toResponse();
    }

    public function destroy()
    {
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
}
