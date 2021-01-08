<?php
namespace App\Common\Helpers;

use DB;
use App\Models\BatteryCategory;
use App\Models\AreaPrice;
use App\Models\AreaPriceLog;

class BatteryHelper {

    /**
     * 快照
     * @param $current
     * @param $before
     */
    public static function viaSnapPrice($current, $before) {
        foreach ($current as $key => $value) {
            if(isset($before[$key]) && $value['id'] == $before[$key]['id']) {
                $current[$key]['before_price'] = $before[$key]['price'];
            } else {
                $current[$key]['before_price'] = $current[$key]['price'];
            }
        }
        return $current;
    }
    
    /**
     * 之前
     * @param $current
     * @param $before
     */
    public static function viaBeforePrice($current, $before) {
        foreach ($current as $key => $value) {
            if(isset($before[$key]) && $value['id'] == $before[$key]['id']) {
                $current[$key]['price'] = $before[$key]['price'];
            }
        }
        return $current;
    }
    
    /**
     * 最优
     * @param $current
     * @param $before
     */
    public static function viaBestPrice($current, $before) {
        foreach ($current as $key => $value) {
            if(isset($before[$key]) && $value['id'] == $before[$key]['id']) {
                if($value->price < $before[$key]['price']) $current[$key]['price'] = $before[$key]['price'];
            }
        }
        return $current;
    }
    
    /**
     * 子树
     * @param unknown $models
     */
    public static function toChildren($models) {
        return Tree::toChildren($models);
    }
    
    /**
     * 树
     * @param $models
     */
    public static function lastChildren($models) {
        $models = static::toChildren($models);
        return static::_lastChildren($models);
    }
    
    private static function _lastChildren($models) {
        $data = [];
        foreach ($models as $model) {
            if($model->children) {
                $data = array_merge($data, static::_lastChildren($model->children));
            } else {
                $data[] = $model;
            }
        }
        return $data;
    }
    
    public static function currentCategory($model) {
        $area = $model->user->getFields('area');
        $data = AreaPrice::whereIn('area_id', $area)->orderBy(DB::raw('FIELD(area_id,' . implode(',', $area) . ')'), 'DESC')->get()->groupBy('category_id');
        foreach ($data as $key => $value ) {
            $data[$key] = $value->first();
        }
        $models = BatteryCategory::all();
        $models->map(function ($item, $key) use ($data) {
            if(isset($data[$item->id])) {
                $item->price = $data[$item->id]->price;
            }
            return $item;
        });
        return $models;
    }
    
    public static function beforeCategory($model, $timeField = 'created_at') {
        $area = $model->user->getFields('area');
        $time = strtotime($model->$timeField);
        $sub = AreaPriceLog::whereIn('area_id', $area)
        ->where('created_at', '<=', $time)
        ->orderBy('created_at', 'DESC');
        $data = DB::table(DB::raw("({$sub->toSql()}) as sub"))
        ->select(DB::raw('sub.*'))
        ->mergeBindings($sub->getQuery())->addBinding($model->site_id)
        ->groupBy('area_id')
        ->groupBy('category_id')
        ->orderBy(DB::raw('FIELD(area_id,' . implode(',', $area) . ')'), 'DESC')
        ->get();
        $data = collect($data)->groupBy('category_id');
        foreach ($data as $key => $value ) {
            $data[$key] = $value->first();
        }
        $models = BatteryCategory::join('battery_category_logs AS log', 'battery_categories.id', '=', 'log.category_id')
        ->select([
                'battery_categories.id',
                'battery_categories.pid',
                'battery_categories.name',
                'battery_categories.alias',
                'battery_categories.sort',
                'battery_categories.deep',
                'log.price',
        ])
        ->whereIn('log.id', function ($query) use ($time) {
            $query->select(DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(`id` ORDER BY `created_at` DESC),',',1)"))
            ->from('battery_category_logs as log')
            ->where('log.created_at', '<=', $time)
            ->groupBy('log.category_id');
        })
        ->get();
        $models->map(function ($item, $key) use ($data) {
            if(isset($data[$item->id])) {
                $item->price = $data[$item->id]->price;
            }
            return $item;
        });
        return $models;
    }

    public static function currentCategoryArea($area) {
        $data = AreaPrice::whereIn('area_id', $area)->orderBy(DB::raw('FIELD(area_id,' . implode(',', $area) . ')'), 'DESC')->get()->groupBy('category_id');
        foreach ($data as $key => $value ) {
            $data[$key] = $value->first();
        }
        $models = BatteryCategory::all();
        $models->map(function ($item, $key) use ($data) {
            if(isset($data[$item->id])) {
                $item->price = $data[$item->id]->price;
            }
            return $item;
        });
        return $models;
    }
}