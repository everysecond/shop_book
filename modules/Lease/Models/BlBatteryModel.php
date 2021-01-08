<?php

namespace Modules\Lease\Models;

use Modules\Lease\Models\BaseModel as Model;

class BlBatteryModel extends Model
{
    public static function modelParams()
    {
        $data = self::query()->select('id','name','weight','price')->get()->toArray();
        $arr = array();
        foreach ($data as $datum) {
            $arr[$datum['id']] = $datum;
        }
        return $arr;
    }
}
