<?php

namespace Modules\Manage\Http\Middleware;

use App\Models\Manager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PermissionApi
{
    public function handle(Request $request, Closure $next)
    {

        $access_token = $request->header('access_token', $request->access_token);
        $manager = Manager::where('access_token',$access_token)->first();
        $route = $request->route()->getName();

        $request->manager = $manager;

        if ($manager->cans($route)) {
            return $next($request);
        } else {

            if ($request->expectsJson()) {
                return response()->json([
                    'code' => 0,
                    'msg' => '非常抱歉，您没有访问权限，请联系管理员添加！'
                ]);

            }

            return response()->json([
                'code' => 0,
                'msg' => '非常抱歉，您没有访问权限，请联系管理员添加！'
            ]);
        }
    }
}
