<?php

namespace App\Http\Controllers;

use App\Domains\User\Entities\User;
use App\Domains\User\Repositories\UserRepository;
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
        if (!UserRepository::exists()) {
            return $this->redirectRoute('auth.signup');
        }

        if (auth()->check()) {
            return $this->redirectRoute('admin.dashboard');
        }

        $rememberToken = cookie()->get('remember_token');
        if ($rememberToken) {
            $user = UserRepository::make()->findByRememberToken($rememberToken);
            if ($user) {
                session()->setUserId($user->id);
                return $this->redirectRoute('admin.dashboard');
            }
        }

        return $this->render('home.pages.auth.signin');
    }

    // =====================================================
    // 회원가입
    // =====================================================
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
            return $this->renderError('auth.register', '회원가입에 실패했습니다.');
        }

        return $this->render('auth.register', [
            'user' => $user
        ], '회원가입에 성공했습니다.');
    }

    // =====================================================
    // 로그인
    // =====================================================
    public function login(Request $request)
    {
        if (UserRepository::make()->attemptRememberTokenLogin()) {
            return $this->render('자동로그인에 성공하였습니다.');
        }

        $request->validateOrFail([
            'email'    => 'required|email|maxLength:100',
            'password' => 'required|string|minLength:8|maxLength:100',
        ]);

        $credentials = $request->safe(['email', 'password']);

        if (!auth()->attempt($credentials)) {
            return $this->renderError('auth.error', '이메일 또는 비밀번호가 올바르지 않습니다.');
        }

        $user = auth()->user();
        
        session()->regenerate();
        UserRepository::make()->setUserToSession($user);
        UserRepository::make()->processRememberMe($request, $user);

        return $this->render('auth.success', ['user' => $user], '로그인에 성공하였습니다.');
    }

    // =====================================================
    // 로그아웃
    // =====================================================
    public function logout()
    {
        $user = UserRepository::find(session()->userId());

        if ($user) {
            UserRepository::make()->deleteRememberToken($user);
        }

        auth()->logout();
        
        return $this->respondWith()
            ->success(true)
            ->message('로그아웃 되었습니다.')
            ->redirectRoute('auth.signin')
            ->send();
    }

    // =====================================================
    // 프로파일
    // =====================================================
    public function me()
    {
        $userId = session()->userId();

        if (!$userId) {
            return $this->renderError('auth.error', '로그인이 필요합니다.', [], 401);
        }

        $user = UserRepository::find($userId);

        if (!$user) {
            return $this->renderError('auth.error', '사용자를 찾을 수 없습니다.', [], 404);
        }

        return $this->render('auth.me', ['user' => $user->toArray()]);
    }
}
