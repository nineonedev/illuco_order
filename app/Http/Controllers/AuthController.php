<?php

namespace App\Http\Controllers;

use App\Domains\User\Entities\Admin;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Entities\Employee;
use App\Domains\User\Entities\User;
use App\Domains\User\Repositories\AdminRepository;
use App\Domains\User\Repositories\UserRepository;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\User\RegisterUserService;
use App\Services\User\UpdateUserService;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Framework\Support\Exceptions\Http\UnauthenticatedException;

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
        $admin = new Admin();

        $result = (new RegisterUserService($admin))
            ->runInTransaction($request->safe());

        return $result->toResponse();
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
        $admin = AdminRepository::make()->findOrFail($id);
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
            ->with([User::morphType()])
            ->query()
            ->find(auth()->id());

        if (!$user) {
            throw new UnauthenticatedException();
        }

        $userable = $user->userable;
        $userable->setRelation(User::alias(), $user); 

        switch (true) {
            case $userable instanceof Admin:
                return $this->render('home.pages.auth.me', [$userable::alias() => $userable]);

            case $userable instanceof Dealer:
                return $this->render('admin.pages.dealers.edit', [$userable::alias() => $userable]);

            case $userable instanceof Employee:
                return $this->render('admin.pages.employees.edit', [$userable::alias() => $userable]);

            default:
                throw new \RuntimeException('Unknown user type.');
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
