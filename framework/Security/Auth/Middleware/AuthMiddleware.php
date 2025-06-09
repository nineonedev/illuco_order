<?php

namespace Framework\Security\Auth\Middleware;

use App\Domains\User\Repositories\UserRepository;
use Closure;
use Framework\Http\ApiResponse;
use Framework\Http\Contracts\MiddlewareInterface;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Security\Auth\Exceptions\UnauthenticatedException;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next, ...$args): Response
    {
        if (
            !auth()->check()
            && !UserRepository::new()->attemptRememberTokenLogin()
        ) {
            return $request->isJsonRequest()
                ? ApiResponse::fail("Unauthorized", [], 401)
                : redirect_route('home');
        }

        return $next($request);
    }
}