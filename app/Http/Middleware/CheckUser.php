<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
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
            } else {
                if (!$request->ajax()) {
                    return redirect("/login");
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
        if($Auth->role_id == 1){
            return redirect("/manage");
        }else{
            return redirect("/report");
        }
    }
}
