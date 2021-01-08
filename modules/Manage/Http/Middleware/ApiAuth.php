<?php

namespace Modules\Manage\Http\Middleware;

use App\Models\Manager;
use Closure;
use Illuminate\Http\Request;

class ApiAuth
{

    const TIMES = 24 * 3600 * 3;    //登录有效期

    public function handle(Request $request, Closure $next)
    {

        $time = time();
        $limitTime = static::TIMES;
        $access_token = $request->header('access_token', $request->access_token);

        if (empty($access_token)) {
            return response()->json([
                'code' => 401,
                'msg'  => '请重新登录'
            ]);
        }

        $is_Login = Manager::where('access_token', $access_token)->first();

        if (empty($is_Login)) {
            return response()->json([
                'code' => 401,
                'msg'  => '请重新登录'
            ]);
        }

        if ($is_Login->access_at + $limitTime < $time) {
            return response()->json([
                'code' => 401,
                'msg'  => '登录超时，请重新登录'
            ]);
        }

        return $next($request);
    }
}
