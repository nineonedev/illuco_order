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

        $perpage = $request->query('perpage', 15);
        $page = $request->query('page', 1); 

        // $query->when($keyword = $request->query('q'), function ($q) use ($keyword) {
        //     $q->whereHas('user', function ($userQuery) use ($keyword) {
        //         $userQuery->where('name', 'like', "%{$keyword}%")
        //                   ->orWhere('email', 'like', "%{$keyword}%");
        //     });
        // });

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

        return $this->render('admin.pages.employees.edit', [
            'employee' => $employee,
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
}
