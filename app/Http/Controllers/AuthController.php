<?php

namespace App\Http\Controllers;

use App\Domains\User\Entities\Admin;
use App\Domains\User\Entities\User;
use App\Domains\User\Repositories\UserRepository;
use App\Http\Requests\User\LoginFormRequest;
use App\Http\Requests\User\RegisterFormRequest;
use App\Services\User\RegisterUserService;
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
        if (!UserRepository::exists()) {
            return $this->responseWith()
                ->redirectRoute('auth.signup')
                ->send();
        }

        if (auth()->check()) {
            return $this->responseWith()
                ->success(true)
                ->message('로그인에 성공하였습니다.')
                ->redirectRoute('admin.dashboard')
                ->send();
        }

        return $this->render('home.pages.auth.signin');
    }

    public function register(RegisterFormRequest $request)
    {
        $admin = new Admin(); 

        $service = new RegisterUserService($admin);
        $result = $service->runInTransaction($request->safe());

        return $result->toResponse();
    }

    public function login(LoginFormRequest $request)
    {
        if (auth()->check()) {
            return $this->render(null, [], lang('validation.already_logged_in'));
        }

        $credentials = $request->safe();

        if (!auth()->attempt($credentials)) {
            return $this->renderError(null, lang('validation.login_failed'))
                ->withInput($request->all());
        }
        
        $user = auth()->user();

        return $this->responseWith()
            ->success(true)
            ->redirectRoute('admin.dashboard')
            ->data(['user' => $user])
            ->message(lang('validation.login_success'))
            ->send();
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


    public function edit()
    {
        $id = auth()->id();
        $user = UserRepository::with([User::morphType()])->find($id);

        if (!$user) {
            throw new UnauthenticatedException();
        }

        return $this->render('home.pages.auth.me', ['user' => $user]);
    }


    public function update()
    {
        
    }
}
