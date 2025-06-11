<?php

namespace App\Http\Controllers;

use App\Domains\User\Entities\User;
use App\Domains\User\Repositories\UserRepository;
use App\Domains\User\Services\RememberToken;
use Framework\Http\Request;
use Framework\Routing\Controller;

class AuthController extends Controller
{
    public function signup()
    {
        return $this->render('home.pages.auth.signup');
    }

    public function signin()
    {
        if (RememberToken::make()->check() || auth()->check()) {
            return $this->responseWith()
                ->success(true)
                ->message('로그인에 성공하였습니다.')
                ->redirectRoute('admin.dashboard')
                ->send();
        }

        return $this->render('home.pages.auth.signin');
    }

    public function register(Request $request)
    {
        $request->validateOrFail([
            'name'     => 'required|string|maxLength:50',
            'email'    => 'required|email|maxLength:100|unique:users',
            'password' => 'required|string|minLength:8|maxLength:100',
        ]);

        $user = User::make($request->safe());
        $user = UserRepository::make()->save($user);

        if (!$user) {
            return $this->renderError(null, '회원가입에 실패했습니다.');
        }

        return $this->render(null, [
            'user' => $user
        ], '회원가입에 성공했습니다.');
    }

    public function login(Request $request)
    {
        if (RememberToken::make()->check()) {
            return $this->render(null, [], '자동로그인에 성공하였습니다.');
        }

        if (auth()->check()) {
            return $this->render(null, [], '이미 로그인되어 있습니다.');
        }

        $request->validateOrFail([
            'email'    => 'required|email|maxLength:100',
            'password' => 'required|string|minLength:8|maxLength:100',
        ]);

        $credentials = $request->safe(['email', 'password']);

        if (!auth()->attempt($credentials)) {
            return $this->renderError(null, '이메일 또는 비밀번호가 올바르지 않습니다.');
        }
        
        $user = auth()->user();
        RememberToken::make()->handleRememberMe($request, $user);

        return $this->responseWith()
            ->success(true)
            ->redirectRoute('admin.dashboard')
            ->data(['user' => $user])
            ->message('로그인에 성공하였습니다.')
            ->send();
    }

    public function logout()
    {
        $user = auth()->user();

        if ($user) {
            RememberToken::make()->delete($user);
        }

        auth()->logout();

        return $this->responseWith()
            ->success(true)
            ->message('로그아웃 되었습니다.')
            ->redirectRoute('home')
            ->send();
    }

    public function me()
    {
        $id = auth()->id();
        $user = UserRepository::find($id); 

        if (!$user) {
            return $this->renderError(null, '로그인이 필요합니다.', [], 401);
        }

        return $this->render('auth.me', ['user' => $user->toArray()]);
    }
}
