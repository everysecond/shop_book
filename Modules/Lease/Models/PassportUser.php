<?php

namespace Modules\Lease\Models;
use Modules\Lease\Models\BaseModel as Model;

class PassportUser extends Model
{
    public function agents()
    {
        return $this->hasMany(PassportUserAgent::class,"user_id","id");
    }
}
