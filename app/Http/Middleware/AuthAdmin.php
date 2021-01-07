<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $Auth = Session::get('adminInfo');
        if (!isset($Auth) || empty($Auth)) {  //验证是否登录
            //判断用户是否存在cookie
            $adminInfo = Cookie::get('adminInfo');
            if (isset($adminInfo->id)) {
                Session::put("adminInfo", $adminInfo);
                $Auth = Session::get('adminInfo');
            } else {
                if (!$request->ajax()) {
                    return redirect("");
                } else {
                    $res = array(
                        "msg" => "未登录",
                        "code" => 1002,
                    );
                    return Response::make($res);
                }
            }
        } else { //当为登录状态时刷新过期时间并验证是否授权
            Session::put("adminInfo", $Auth);
        }
        return $next($request);
        
        //判断是否登陆,如未登录则重定向到登陆页
//        if (empty(\Session::get('adminInfo'))) {
//            return redirect('/login');
//        }
//        //如已登陆则执行下一步
//        return $next($request);
        
    }
}
