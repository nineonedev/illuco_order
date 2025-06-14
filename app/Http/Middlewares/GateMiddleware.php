<?php

namespace App\Http\Middlewares;

use Closure;
use Framework\Http\Contracts\MiddlewareInterface;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Support\Facades\Gate;
use RuntimeException;

class GateMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next, ...$args): Response
    {
        $ability = $args[0] ?? null;
        $params = array_slice($args, 1);

        if (!$ability || !Gate::allows($ability, $params)) {
            throw new RuntimeException("Forbidden: You do not have permission to perform [$ability].");
        }

        return $next($request); 
    }
}