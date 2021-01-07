<?php

namespace Modules\Lease\Models;

use Modules\Lease\Models\BaseModel as Model;

class BlAppDown extends Model
{
    protected $table = "bl_app_down";
    //租点用户端
    const APP_TYPE_ONE = 1;
    //网点用户端
    const APP_TYPE_TWO = 2;
}
