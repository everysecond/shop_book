<?php

namespace Modules\Lease\Models;

use Illuminate\Support\Arr;
use Modules\Lease\Models\BaseModel as Model;

class BlBatteryGroupModel extends Model
{
    public static function groupParams()
    {
        $modelParams = BlBatteryModel::modelParams();
        $data        = self::query()->select('id', 'model_id', 'name', 'num', 'price')->get()->toArray();
        $arr         = array();
        foreach ($data as $datum) {
            $arr[$datum['id']] = [
                'name'   => $datum['name'],
                'num'    => $datum['num'],
                'weight' => $datum['num'] * Arr::get($modelParams, $datum['model_id'].".weight", 0),
                'price'  => $datum['price']
            ];
        }
        return [
            'model' => $modelParams,
            'group' => $arr
        ];
    }
}
