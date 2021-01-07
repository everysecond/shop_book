<?php

namespace Modules\Manage\Http\Middleware;

use App\Models\Permission\Role;
use Closure;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class PermissionsAdmin
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
        //获取授权组权限
        if ($Auth) {
            $role = new Role();
            $isAuth = $role->getIsAuth($Auth->role_id);
            if (!$isAuth) {
                if (!$request->ajax()) {
                    return redirect(adminurl("/unauthorized"));
                } else {
                    $res = array(
                        "msg" => "未授权",
                        "code" => 1003,
                    );
                    return Response::make($res);
                }
            }
        } else {
            $res = array(
                "msg" => "未授权",
                "code" => 1003,
            );
            return Response::make($res);
        }
        //检测是否有指定菜单操作权限
        $menuAndActionPath = explode("/", preg_replace("/" . env("BACKSTAGE_PREFIX") . "/", "", $request->path(), 1));
        $menuPath = isset($menuAndActionPath[1]) ? $menuAndActionPath[1] : "";
        $actionPath = isset($menuAndActionPath[2]) ? $menuAndActionPath[2] : "";
        //验证是否有指定操作的访问权限
        $menuIsAuth = $role->getIsMenuAndActionAuth($Auth->role_id, $menuPath, $actionPath);
        if (!$menuIsAuth) {
            if (!$request->ajax()) {
                return redirect(adminurl("/unauthorized"));
            } else {
                $res = array(
                    "msg" => "未授权",
                    "code" => 1003,
                );
                return Response::make($res);
            }
        }
        return $next($request);
    }
}
