<?php

namespace Modules\Kood\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Kood\Models\BaseModel as Model;

class Agent extends Model
{
    use SoftDeletes;
}
