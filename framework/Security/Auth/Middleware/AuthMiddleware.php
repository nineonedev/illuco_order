<?php

namespace Framework\Security\Auth\Middleware;

use Closure;
use Framework\Http\Request;
use Framework\Http\Contracts\MiddlewareInterface;
use Framework\Http\Contracts\ResponseInterface;
use App\Domains\User\Services\RememberToken;
use Framework\Http\Response;
use Framework\Support\Exceptions\Http\UnauthorizedException;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next, ...$args): ResponseInterface
    {
        // throw new UnauthorizedException('로그인이 필요합니다.');

        if (!auth()->check() && !RememberToken::make()->check()) {
            return Response::redirectRoute('auth.signin');
        }

        return $next($request);
    }
}
