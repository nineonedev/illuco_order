<?php

namespace App\Http\Controllers;

use App\Domains\User\Entities\Admin;
use App\Domains\User\Entities\User;
use App\Domains\User\Repositories\UserRepository;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
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

    public function edit()
    {
        $user = UserRepository::make()
            ->with([User::morphType()])
            ->query()
            ->find(auth()->id());

        if (!$user) {
            throw new UnauthenticatedException();
        }

        return $this->render('home.pages.auth.me', ['user' => $user]);
    }

    public function update()
    {
        // TODO: 사용자 정보 수정 로직
    }

    /**
     * 인증 후 대시보드 리다이렉션 응답
     */
    protected function redirectToDashboard(string $message, ?User $user = null)
    {
        return $this->responseWith()
            ->success(true)
            ->message($message)
            ->redirectRoute('admin.dashboard')
            ->data(['user' => $user])
            ->send();
    }
}
