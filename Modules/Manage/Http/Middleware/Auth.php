<?php

namespace Modules\Manage\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Auth
{
    public function handle(Request $request, Closure $next)
    {
        if (!\Auth::guard('manage')->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'code' => 401,
                    'msg' => '请重新登录'
                ]);
            } else {
                return \Redirect::to(route('login'))->withMessage('请重新登录');
            }
        }

        return $next($request);
    }
}
