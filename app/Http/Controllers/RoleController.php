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
        $query = RoleRepository::queryStatic();

        // 권한명 검색
        $query->when(
            $name = $request->query('name'),
            fn($q) => $q->where('name', 'like', "%{$name}%")
        );

        // 설명 검색
        $query->when(
            $description = $request->query('description'),
            fn($q) => $q->where('description', 'like', "%{$description}%")
        );

        // 정렬
        $sort = $request->query('sort');
        if ($sort) {
            switch ($sort) {
                case 'name_asc':
                    $query->orderBy('name');
                    break;
                case 'name_desc':
                    $query->orderByDesc('name');
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at');
                    break;
                case 'created_at_desc':
                    $query->orderByDesc('created_at');
                    break;
                default:
                    $query->orderByDesc('created_at');
                    break;
            }
        } else {
            $query->orderByDesc('created_at');
        }

        $perPage = $request->query('perPage', 15);
        $page = $request->query('page', 1);

        $roles = $query->paginate($perPage, $page);

        return $this->render('admin.pages.roles.index', [
            'roles' => $roles,
            'query' => $request->query(),
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
        $data = $request->only(['name', 'label', 'description']);
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
        $data = $request->only(['name', 'label', 'description']);
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
