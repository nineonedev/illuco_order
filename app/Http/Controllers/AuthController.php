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
        return $this->view('home.pages.auth.signup');
    }

    public function signin()
    {
        if (!UserRepository::exists()) {
            return redirect_route('auth.signup');
        }
        
        if (auth()->user()) {
            return redirect_route('admin.dashboard');
        }
        
        $rememberToken = cookie()->get('remember_token');
        if ($rememberToken) {
            $user = UserRepository::findByRememberToken($rememberToken);
            if ($user) {
                session()->set('user_id', $user->id);
                return redirect_route('admin.dashboard');
            }
        }

        return $this->view('home.pages.auth.signin');
    }

    /**
     * ======================================================================
     * 회원가입
     * ======================================================================
     */
    public function register(Request $request)
    {
        $request->validateOrFail([
            'name'     => 'required|string|maxLength:50',
            'email'    => 'required|email|maxLength:100|unique:users',
            'password' => 'required|string|minLength:8|maxLength:100',
        ]);
        // $credentials = [
        //     'name' => $request->input('name'),
        //     'email' => $request->input('email'),
        //     'password' => password_hash($request->input('password'), PASSWORD_BCRYPT, ['cost' => 10]),
        // ];

        $user = User::new($request->safe());
        $user = UserRepository::new()->save($user);

        if (!$user) {
            return $this->apiFail('회원가입에 실패했습니다.');
        }

        return $this->apiSuccess([
            'user' => $user->toArray(),
        ], '회원가입에 성공했습니다.');
    }

    /**
     * ======================================================================
     * 로그인
     * ======================================================================
     */
    public function login(Request $request)
    {
       // 자동로그인
        if (UserRepository::new()->attemptRememberTokenLogin()) {
            return $this->apiSuccess('자동로그인에 성공하였습니다.');
        }

        // 폼 검증
        $request->validateOrFail([
            'email'    => 'required|email|maxLength:100',
            'password' => 'required|string|minLength:8|maxLength:100',
        ]);

        $credentials = $request->safe(['email', 'password']);

        // 이 한 줄로 이메일, 비밀번호 체크, 세션 세팅까지 됨!
        if (!auth()->attempt($credentials)) {
            return $this->apiFail('이메일 또는 비밀번호가 올바르지 않습니다.');
        }

        $user = auth()->user();

        // 세션, 리멤버 토큰 등 부가처리
        session()->regenerate();
        UserRepository::new()->setUserToSession($user);
        UserRepository::new()->processRememberMe($request, $user);

        return $this->apiSuccess('로그인에 성공하였습니다.');
    }

    /**
     * ======================================================================
     * 로그아웃
     * ======================================================================
     */
    public function logout(Request $request)
    {
        $userId = session()->get('user_id');
        $user = $userId ? UserRepository::find($userId) : null;

        if ($user) {
            UserRepository::new()->deleteRememberToken($user);
        }
        
        auth()->logout();

        if ($request->isJsonRequest()) {
            return $this->apiSuccess(null, '로그아웃 되었습니다.');
        }

        return redirect_route('auth.signin');
    }

    /**
     * ======================================================================
     * 내 정보 조회
     * ======================================================================
     */
    public function me()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->apiFail('로그인이 필요합니다.', [], 401);
        }

        $user = UserRepository::find($userId);

        if (!$user) {
            return $this->apiFail('사용자를 찾을 수 없습니다.', [], 404);
        }

        return $this->apiSuccess(['user' => $user->toArray()]);
    }
}
