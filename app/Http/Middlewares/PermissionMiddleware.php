<?php

namespace App\Http\Middlewares;

use Closure;
use Framework\Http\Request;
use Framework\Http\Contracts\MiddlewareInterface;
use Framework\Http\Contracts\ResponseInterface;
use Framework\Support\Exceptions\Http\ForbiddenException;
use Framework\Support\Exceptions\Http\UnauthorizedException;
use Framework\Security\Auth\Access\Gate;

/**
 * 권한 체크용 미들웨어
 *
 * 라우트에서 ->middleware('permission:order.read') 처럼 사용 가능
 */
class PermissionMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next, ...$args): ResponseInterface
    {
        // // 로그인 여부 우선 체크
        // if (!auth()->check()) {
        //     throw new UnauthorizedException('로그인이 필요합니다.');
        // }

        // if (empty($args)) {
        //     throw new ForbiddenException('권한 설정이 누락되었습니다.');
        // }

        // // 라우트에서 전달된 인자: 예) permission:order.read
        // $permission = $args[0];

        // // permission 문자열 분리
        // [$resource, $action] = array_pad(explode('.', $permission, 2), 2, null);

        // if (!$resource || !$action) {
        //     throw new ForbiddenException('권한 형식이 잘못되었습니다. (예: order.read)');
        // }

        // // Gate 권한 검사
        // $user = user();
        // $allowed = Gate::allows("{$resource}.{$action}", [$user]);

        // if (!$allowed) {
        //     throw new ForbiddenException("{$resource} 리소스에 대한 {$action} 권한이 없습니다.");
        // }

        return $next($request);
    }
}
