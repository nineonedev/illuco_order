<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Repositories\RoleRepository;
use App\Domains\User\Entities\Employee;
use App\Domains\User\Entities\User;
use App\Domains\User\Repositories\EmployeeRepository;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\User\RegisterUserService;
use App\Services\User\UpdateUserService;
use App\Services\User\DeleteUserService;
use Exception;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class EmployeeController extends Controller
{
    protected function repo(): EmployeeRepository
    {
        return EmployeeRepository::make();
    }

    public function index(Request $request)
    {
        $query = $this->repo()->with([User::alias()])->query();

        $query->when($keyword = $request->query('q'), function ($q) use ($keyword) {
            $q->whereHas('user', function ($userQuery) use ($keyword) {
                $userQuery->where('name', 'like', "%{$keyword}%")
                          ->orWhere('email', 'like', "%{$keyword}%");
            });
        });

        return $this->render('admin.pages.employees.index', [
            'employees' => $query->paginate($request->query('perpage', 15), $request->query('page', 1)),
            'query'     => $request->query(),
        ]);
    }

    public function create()
    {
        return $this->render('admin.pages.employees.create');
    }

    public function edit(string $id)
    {
        $employee = $this->repo()->with([User::alias()])->find($id);

        if (!$employee) {
            throw new RuntimeException("직원 정보를 찾을 수 없습니다.");
        }

        return $this->render('admin.pages.employees.edit', [
            'employee' => $employee,
        ]);
    }

    public function store(RegisterRequest $request)
    {
        $employee = new Employee($request->all());
        $result = (new RegisterUserService($employee))
            ->runInTransaction($request->safe());

        // 3. 'dealer' 역할을 가져옴
        $employeeRole = RoleRepository::make()
            ->with(['users']) // 'users' 관계를 가져옴
            ->query()
            ->where('name', 'employee')
            ->first();

        // 4. Role이 존재하면 사용자 연결
        if ($employeeRole) {
            $employeeRole->users()->attach($employee->user);
        }

        return $result->toResponse();
    }

    public function update(string $id, UpdateUserRequest $request)
    {
        $employee = $this->repo()->findOrFail($id);
        $data = $request->safe();

        if (empty($data['password'])) {
            unset($data['password']); 
        }

        $employee->fill($data);
        $result = (new UpdateUserService($employee))
            ->runInTransaction($data);

        return $result->toResponse();
    }

    public function destroy(string $id)
    {
        $employee = $this->repo()->findOrFail($id);
        $result = (new DeleteUserService($employee))->runInTransaction([]);
        return $result->toResponse();
    }

    protected function save(Employee $employee): void
    {
        if (!$this->repo()->save($employee)) {
            throw new RuntimeException("직원 저장에 실패했습니다.");
        }
    }
}
