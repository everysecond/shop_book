<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2019/7/10
 * Time: 10:48
 */

namespace App\Models;

class ManagerPermission extends Model
{
    public function parent()
    {
        return $this->belongsTo(ManagerPermission::class, 'pid');
    }

    public function actions()
    {
        return $this->hasMany(ManagerPermissionAction::class, 'permission_id');
    }
}