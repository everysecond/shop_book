<?php
namespace App\Common\Helpers;

use App\Models\Area;

class AreaHelper {

    public static function field($model) {
        return [
            'province_id' => array_get($model, 'province_id'),
            'city_id' => array_get($model, 'city_id'),
            'county_id' => array_get($model, 'county_id'),
            'town_id' => array_get($model, 'town_id')
        ];
    }

    public static function selects($area, &$pid, $provinces = []) {
        $pid = 0;
        $selects = [];
        foreach ($area as $key => $value) {
            if ($pid == 0 && !empty($provinces)) {
                $item = Area::instance()->whereIn('id', $provinces)->lists('name', 'id');
            } else {
                $item = Area::instance()->children($pid)->lists('name', 'id');
            }
            $selects[$key] = ['items' => $item, 'selected' => $value];
            if (empty($value)) break;
            $pid = $value;
        }
        return $selects;
    }

    public static function fullName($model) {
        $area = static::field($model);
        $full = Area::whereIn('id', $area)->orderBy('deep', 'ASC')->lists('name', 'id')->toArray();
        return implode(' - ', $full);
    }
    
    public static function withFullName($model) {
        $names[] = $model->province->name;
        $names[] = $model->city->name;
        $names[] = $model->county->name;
        $names[] = $model->town->name;
        return implode(' - ', array_filter($names));
    }

    public static function fullId($model, $type = ',') {
        $area = static::field($model);
        unset($area[array_search('0', $area)]);
        return implode($type, $area);
    }
}