<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2019/7/10
 * Time: 10:48
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Hash;
use Modules\Manage\Repositories\ManagerRepository;

class Manager extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, SoftDeletes;

    CONST STATUS_NORMAL = 1;
    CONST STATUS_LOCKED = 2;

    protected $hidden = ['password', 'remember_token', 'deleted_at'];

    protected $cachePermissionActions = null;

    public function cans($route)
    {
        $exceptRoute = config('entrust.fill_route');
        if(in_array($route,$exceptRoute)){
            return true;
        }
        return $this->can($route);
    }

    public function roles()
    {
        return $this->belongsToMany(ManagerRole::class, 'manager_has_roles', 'manager_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(ManagerPermission::class, 'manager_has_permissions', 'manager_id', 'permission_id');
    }

    public function checkPassword($password)
    {
        return Hash::check($password, $this->getAuthPassword());
    }

    public function isNormal()
    {
        return $this->status == SELF::STATUS_NORMAL;
    }

    public function isLocked()
    {
        return $this->status == SELF::STATUS_LOCKED;
    }

    public function isSuper()
    {
        return !!$this->roles->where('code', 'super')->count();
    }

    //是否能看全国区域数据
    public function isGlobal()
    {
        return !!$this->roles->where('name', '全国')->count();
    }

    public function getPermissionActionsAttribute()
    {
        if (is_null($this->cachePermissionActions)) {
            $this->cachePermissionActions = app(ManagerRepository::class)->getPermissionActions($this);
        }
        return $this->cachePermissionActions;
    }

    public function last_login_at()
    {
        return $this->hasOne(ManagerLog::class, 'manager_id', 'id')
            ->where('route', 'login.submit')->orderBy('created_at', 'desc');
    }
}