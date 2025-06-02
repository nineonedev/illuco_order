<?php

namespace Framework\Security\Auth\Middleware;

use Closure;
use Framework\Http\Contracts\MiddlewareInterface;
use Framework\Http\Request;
use Framework\Http\Response;
use RuntimeException;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next, ...$args): Response
    {
        if (!auth()->check()) {
            // throw new RuntimeException("Unauthorized: You must be logged in."); 
        }

        return $next($request);
    }
}