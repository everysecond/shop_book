<?php

namespace Modules\Manage\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Permission
{
    public function handle(Request $request, Closure $next)
    {
        $manager = Auth::user();

        $route = $request->route()->getName();

        if ($manager->cans($route)) {
            return $next($request);
        } else {
            if ($request->expectsJson()) {
                return response()->json([
                    'code' => 403,
                    'msg' => '非常抱歉，您没有访问权限！'
                ]);
            }
            throw new AccessDeniedHttpException('非常抱歉，您没有访问权限！');
        }
    }
}
