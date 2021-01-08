<?php

namespace Modules\Lease\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Lease\Models\BaseModel as Model;

class BlAgent extends Model
{
    use SoftDeletes;
    //租点涉及所有省份
    public static function allProvinces()
    {
        return self::where("pid",0)->orderBy("sort")->pluck("name","id")->toArray();
    }
}
