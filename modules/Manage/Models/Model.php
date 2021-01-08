<?php

namespace Modules\Manage\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    protected $hidden = [
        'password'
    ];
    protected $primaryKey = 'id';

    protected $dateFormat = 'U';

    protected $dates = [
        "created_at",
        "updated_at"
    ];


    public function scopeRecent(Builder $query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
