<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Repositories\RoleRepository;
use App\Domains\User\Entities\User;
use App\Domains\User\Enums\UserType;
use App\Domains\User\Repositories\UserRepository;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\User\UpdateUserService;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = UserRepository::make()
            ->query()
            ->where('type', UserType::EMPLOYEE);

        // 비관리자일 경우 employee 역할만 조회
        if (!user()->isAdmin()) {
            $query->whereHas('roles', function($roleQuery) {
                $roleQuery->where('name', 'employee');
            });
        }

        // 이름 검색
        $query->when(
            $name = $request->query('name'),
            fn($q) => $q->where('name', 'like', "%{$name}%")
        );

        // 이메일 검색
        $query->when(
            $email = $request->query('email'),
            fn($q) => $q->where('email', 'like', "%{$email}%")
        );

        // 연락처 검색
        $query->when(
            $phone = $request->query('phone'),
            fn($q) => $q->where('phone', 'like', "%{$phone}%")
        );

        // 상태 검색
        $query->when(
            $status = $request->query('status'),
            fn($q) => $q->where('status', $status)
        );

        // 정렬 처리
        $sort = $request->query('sort');
        if ($sort) {
            switch ($sort) {
                case 'created_at_asc':
                    $query->orderBy('created_at');
                    break;
                case 'created_at_desc':
                    $query->orderByDesc('created_at');
                    break;
                case 'name_asc':
                    $query->orderBy('name');
                    break;
                case 'name_desc':
                    $query->orderByDesc('name');
                    break;
                case 'email_asc':
                    $query->orderBy('email');
                    break;
                case 'email_desc':
                    $query->orderByDesc('email');
                    break;
                default:
                    $query->orderByDesc('created_at');
                    break;
            }
        } else {
            $query->orderByDesc('created_at');
        }

        $perpage = $request->query('perpage', 15);
        $page = $request->query('page', 1);

        return $this->render('admin.pages.employees.index', [
            'employees' => $query->paginate($perpage, $page),
            'query'     => $request->query(),
        ]);
    }


    public function create()
    {
        return $this->render('admin.pages.employees.create');
    }

    public function edit(int $id)
    {
        $employee = UserRepository::make()->findOrFail($id); 

        if (user()->isAdmin()) {
            $roles = RoleRepository::make()
                ->query()
                ->whereIn('name', ['employee', 'sales'])
                ->get(); 
        } else {
            $roles = [];
        }

        $employee->load(['roles']);
        
        return $this->render('admin.pages.employees.edit', [
            'employee' => $employee,
            'roles' => $roles,
        ]);
    }

    public function store(RegisterRequest $request)
    {
        return $this->runInTransaction(function() use ($request) {
            $user = new User($request->safe());
            $user->type = UserType::EMPLOYEE;

            $user = UserRepository::make()->with(['roles'])->save($user);

            if (!$user) {
                throw new RuntimeException("직원 생성에 실패하였습니다.");
            }

            $role = RoleRepository::make()
                ->query()
                ->where('name', 'employee')
                ->first();

            if (!$role) {
                throw new RuntimeException("직원 권한이 없습니다. [employee]");
            } 
            
            $user->roles()->attach($role);

            return $this->render(null, ['employee' => $user->toArray()], '성공적으로 생성되었습니다.');
        });
    }

     public function update(int $id, UpdateUserRequest $request)
    {
        return $this->runInTransaction(function() use ($id, $request) {
            $employee = UserRepository::make()->findOrFail($id);
            $data = $request->safe();
            if (empty($data['password'])) {
                unset($data['password']); 
            }

            $isActive = $request->body('is_active') ? true : false; 
            $data['is_active'] = $isActive; 

            $employee->fill($data); 
            $employee = UserRepository::make()->save($employee); 
            
            // role 변동시 저장
            $roleId = $request->body('role_id');

            if ($roleId) {
                $employee->roles()->sync([(int) $roleId]);
            }

            if (!$employee) {
                throw new RuntimeException("직원 수정에 실패하였습니다.");
            }

            return $this->render(null, [
                'employee' => $employee,
            ], '정상적으로 수정되었습니다.');
        });
    }

    public function destroy(int $id)
    {
        return $this->runInTransaction(function() use ($id) {
            $employee = UserRepository::make()->findOrFail($id);
            $success = UserRepository::make()->delete($employee); 

            if (!$success) {
                throw new RuntimeException("직원 삭제에 실패하였습니다.");
            }

            return $this->render(null, [], '정상적으로 삭제되었습니다.');
        });
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->body('ids', []);

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            return $this->render(null, [], "삭제할 직원이 없습니다.");
        }

        return $this->runInTransaction(function () use ($ids) {
            $totalDeleted = 0;

            foreach ($ids as $id) {
                $employee = UserRepository::make()->find($id);
                if (!$employee) {
                    continue;
                }

                if (UserRepository::make()->delete($employee)) {
                    $totalDeleted++;
                }
            }

            return $this->render(null, [], "선택된 직원이 삭제되었습니다. (삭제된 수: {$totalDeleted})");
        });
    }

}
