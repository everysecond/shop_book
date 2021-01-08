<?php

namespace Modules\Manage\Http\Middleware;

use App\Models\Manager;
use App\Models\ManagerLog;
use Closure;
use Illuminate\Http\Request;

class ApiManageLog
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request)
    {
        $manager = $request->manager;
        if (empty($manager)) {
            return;
        }

        $routeName = $request->route()->getName();
        $data = $routeName != 'login.submit' ? $request->all() : [];

        ManagerLog::create([
            'route'        => $routeName,
            'name'         => $routeName,
            'manager_id'   => $manager->id,
            'manager_name' => $manager->name,
            'url'          => $request->url(),
            'method'       => $request->method(),
            'ip'           => $request->ip(),
            'data'         => $data,
            'source'       => 'api'
        ]);
    }

}
