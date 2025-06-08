<?php 

namespace App\Http\Controllers;

use App\Domains\User\Repositories\UserRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Framework\Support\Facades\Hash;

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

        return $this->view('home.pages.auth.signin');
    }

    /**
     * ======================================================================
     * 회원가입
     * ======================================================================
     */
    public function register(Request $request)
    {
        // Validate
        $request->validateOrFail([
            'name'     => 'required|string|maxLength:50',
            'email'    => 'required|email|maxLength:100|unique:users',
            'password' => 'required|string|minLength:8|maxLength:100',
        ]);

        $user = UserRepository::new($request->safe())->save();

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
        $request->validateOrFail([
            'email'    => 'required|email|maxLength:100',
            'password' => 'required|string|minLength:8|maxLength:100',
        ]);

        $user = UserRepository::where('email', $request->body('email'))->first();

        // 비밀번호 비교
        if (!$user || !Hash::check($request->body('password'), $user->password)) {
            return $this->apiFail('이메일 또는 비밀번호가 올바르지 않습니다.');
        }
        
        session_store()->start(); 
        session_store()->set('user_id', $user->id); 

        $user->last_login_at = now();
        $user->login_count = (int) $user->login_count + 1; 
        $user->login_ip = request()->http()->ip();

        if (!$user) {
            return $this->apiFail('사용자 저장 실패'); 
        }

        return $this->apiSuccess('로그인에 성공하였습니다.');

    }
    
    /**
     * ======================================================================
     * 로그아웃
     * ======================================================================
     */
    public function logout(Request $request)
    {
        session_store()->start();
        session_store()->invalidate();

        return $this->apiSuccess(null, '로그아웃 되었습니다.');
    }

    public function me()
    {
        session_store()->start();
        $userId = session_store()->get('user_id'); 
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