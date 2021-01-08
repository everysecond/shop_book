<?php
/*
 * @name 系统日志
 * @auth weis
 * @time 2019-6-25
 */

namespace Modules\Manage\Http\Controllers;



use Illuminate\Http\Request;

class LogViewController extends Controller
{
    public function index(Request $request)
    {
        $this->actionLog("查看日志", $request->ip());
        $menuInfo = getMenuFromPath($request->path());
        return view("manage::log.log")
            ->with("thisAction", $menuInfo->url)
            ->with("title", $menuInfo->title)
            ->with("total", 0);
    }
    
}