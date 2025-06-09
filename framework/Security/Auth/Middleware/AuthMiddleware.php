<?php

namespace Framework\Security\Auth\Middleware;

use App\Domains\User\Repositories\UserRepository;
use Closure;
use Framework\Http\Contracts\MiddlewareInterface;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Support\Exceptions\Http\UnauthorizedException;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next, ...$args): Response
    {
        if (!auth()->check() && !UserRepository::make()->attemptRememberTokenLogin()) {
            throw new UnauthorizedException('로그인이 필요합니다.');
        }

        return $next($request);
    }
}
