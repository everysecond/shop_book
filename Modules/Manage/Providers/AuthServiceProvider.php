<?php

namespace Modules\Manage\Providers;

use App\Models\ManagerPermissionAction;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        \DB::enableQueryLog();

        Gate::before(function ($user) {
            if ($user->isSuper()) {
                return true;
            }
        });

        $actions = ManagerPermissionAction::distinct('action')->pluck('action');
        foreach ($actions as $action) {
            Gate::define($action, function ($user) use ($action) {
                return in_array($action, $user->permissionActions);
            });
        }
    }
}
