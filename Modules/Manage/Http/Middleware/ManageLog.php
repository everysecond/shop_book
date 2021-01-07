<?php

namespace Modules\Manage\Http\Middleware;

use App\Models\ManagerLog;
use Closure;
use Illuminate\Http\Request;

class ManageLog
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
    
    public function terminate(Request $request)
    {
        $manager = \Auth::guard('manage')->user();
        if ($request->isMethod('get') || empty($manager)) {
            return;
        }
        
        $routeName = $request->route()->getName();
        $data = $routeName != 'login.submit' ? $request->all() : [];
        
        ManagerLog::create([
            'route' => $routeName,
            'name' => $routeName,
            'manager_id' => $manager->id,
            'manager_name' => $manager->name,
            'url' => $request->url(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'data' => $data
        ]);
    }
    
}
