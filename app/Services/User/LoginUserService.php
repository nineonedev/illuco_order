<?php 

namespace App\Services\User;

use App\Supports\Services\Service;
use Framework\Support\Exceptions\Http\UnauthorizedException;

class LoginUserService extends Service
{
    protected function handle(array $payload)
    {
        $credentials = $request->safe(['email', 'password']);

        

        if (!auth()->attempt($credentials)) {
            throw new UnauthorizedException();
        }
        
        $user = auth()->user();
        // RememberToken::make()->handleRememberMe($request, $user);

        return $this->responseWith()
            ->success(true)
            ->redirectRoute('admin.dashboard')
            ->data(['user' => $user])
            ->message('로그인에 성공하였습니다.')
            ->send();
    }
}