<?php

namespace Modules\Lease\Models;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {
    protected $connection = "mysql_lease";
}
