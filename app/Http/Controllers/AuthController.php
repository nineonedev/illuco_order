<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Repositories\RoleRepository;
use App\Domains\User\Entities\User;
use App\Domains\User\Enums\UserType;
use App\Domains\User\Repositories\UserRepository;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\Auth\SaveRoleService;
use App\Services\User\UpdateUserService;
use Framework\Routing\Controller;
use Framework\Support\Exceptions\Http\UnauthenticatedException;
use RuntimeException;

class AuthController extends Controller
{
    public function signup()
    {
        return $this->render('home.pages.auth.signup');
    }

    public function signin()
    {
        if (!UserRepository::queryStatic()->exists()) {
            return $this->responseWith()
                ->redirectRoute('auth.signup')
                ->send();
        }

        if (auth()->check()) {
            return $this->redirectToDashboard("로그인에 성공하였습니다.");
        }

        return $this->render('home.pages.auth.signin');
    }

    public function register(RegisterRequest $request)
    {
        return $this->runInTransaction(function() use ($request) {

            $user = new User($request->safe());
            $user = UserRepository::make()->with(['roles'])->save($user);

            if (!$user) {
                throw new RuntimeException("가입에 실패하였습니다.");
            }

            $adminRole = RoleRepository::make()
                ->query()
                ->where('name', 'admin')
                ->first();

            if ($adminRole) {
                $user->roles()->attach($adminRole);
                
            } else {
                $adminRole = [
                    'name' => 'admin',
                    'description' => '최고 관리자',
                ];

                $service = new SaveRoleService();
                $result = $service->run([
                    'role' => $adminRole,
                    'permissions' => context()->get('permissions', []),
                ]);

                
                $role = $result->getData()['role'] ?? null;

                if (!$role) {
                    throw new RuntimeException('권한 생성에 실패했습니다.');
                }

                $user->roles()->attach($role);
            }

            return $this->render(null, ['user' => $user], '성공적으로 가입되었습니다.');
        });
    }

    public function login(LoginRequest $request)
    {
        if (auth()->check()) {
            return $this->redirectToDashboard(lang('validation.already_logged_in'));
        }

        $credentials = $request->safe();

        if (!auth()->attempt($credentials)) {
            return $this->renderError(null, lang('validation.login_failed'))
                ->withInput($request->all());
        }

        return $this->redirectToDashboard(lang('validation.login_success'), auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return $this->responseWith()
            ->success(true)
            ->message(lang('validation.logout_success'))
            ->redirectRoute('home')
            ->send();
    }

    public function update(string $id, UpdateUserRequest $request)
    {
        $admin = UserRepository::make()->findOrFail($id);
        $admin->fill($request->safe());
        $data = $request->safe();

        if (empty($data['password'])) {
            unset($data['password']); 
        }

        $result = (new UpdateUserService($admin))
            ->runInTransaction($data);

        return $result->toResponse();
    }

    public function edit()
    {
        $user = UserRepository::make()
            ->query()
            ->find(auth()->id());

        if (!$user) {
            throw new UnauthenticatedException();
        }

        switch ($user->type) {
            case UserType::ADMIN:
                return $this->render('home.pages.auth.me', ['user' => $user]);
            case UserType::DEALER:
                $user->load([UserType::DEALER]);
                return $this->render('admin.pages.dealers.edit', ['dealer' => $user]);

            case UserType::EMPLOYEE:
                return $this->render('admin.pages.employees.edit', ['employee' => $user]);
            default:
                throw new RuntimeException('Unknown user type.');
        }
    }

   
    protected function redirectToDashboard(string $message, ?User $user = null)
    {
        return $this->responseWith()
            ->success(true)
            ->message($message)
            ->redirectRoute('admin.orders.index')
            ->data(['user' => $user])
            ->send();
    }
}
