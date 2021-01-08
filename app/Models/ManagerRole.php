<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ManagerRole extends Model
{
    public function isSuper()
    {
        return $this->code === 'super';
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            ManagerPermission::class,
            'manager_role_has_permissions',
            'role_id',
            'permission_id'
        );
    }
}