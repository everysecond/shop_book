<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/10/15 09:39
 */

namespace Modules\Manage\Models\Service;

use Modules\Manage\Models\Model;

class ServiceStockLog extends Model
{
    //库存区域占比
    public function area($request)
    {
        $type = $request->type;
        if ($type != 0 && !$type) {
            $type = 'total';
        }
        if (!($date = $request->date)) $date = date('Y-m-d', strtotime('-1 day'));
        $data = self::query()->where('date', $date)->first();
        if ($data) {
            $data = json_decode($data->json_data, true);
            $result = [];
            foreach ($data as $pid => $datum) {
                if ($type == 'total') {
                    $result[$pid] = $datum[$type];
                } else {
                    $result[$pid] = $datum[$type]['total'];
                }
            }
        } else {
            return [];
        }
        return $result;
    }

    //各区域库存统计列表
    public function getList($request)
    {
        $req = $request->all();
        $where = [];
        if (isset($req["dateRange"]) && ($dateRange = $req["dateRange"])) {
            $time = explode(' - ', $dateRange);
            if ($time[0] == $time[1]) {
                $where[] = ["date", $time[0]];
            } else {
                $where[] = ['date', '>=', $time[0]];
                $where[] = ['date', '<=', $time[1]];
            }
        }
        $data = self::query()->where($where)->orderBy("date", "desc");
        $count = $data->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();

        $type = $request->type;
        if ($type != 0 && !$type) {
            $type = 'total';
        }
        $provinces = allUserProvinces('total');
        if (isset($provinces[0])) unset($provinces[0]);
        foreach ($data as &$datum) {
            $json = json_decode($datum['json_data'], true);
            $datum['全部区域'] = $type == 'total' ? $json['total'][$type] : $json['total'][$type]['total'];
            foreach ($provinces as $id => $name) {
                if ($type == 'total') {
                    $datum[$name] = isset($json[$id]) ? $json[$id]['total'] : 0;
                } else {
                    $datum[$name] = isset($json[$id]) ? $json[$id][$type]['total'] : 0;
                }
            }
        }
        return ["data" => $data, "count" => $count];
    }

    //电池型号占比
    public function battery($request)
    {
        $type = $request->type;
        if ($type != 0 && !$type) {
            $type = 'total';
        }
        $agentId = $request->agentId;
        if (!($date = $request->date)) $date = date('Y-m-d', strtotime('-1 day'));
        $data = self::query()->where('date', $date)->first();
        if ($data) {
            return json_decode($data->json_data, true)[$agentId][$type];
        } else {
            return [];
        }
    }

    //电池型号库存统计
    public function getBatteryList($request)
    {
        $req = $request->all();
        $where = [];
        if (isset($req["dateRange"]) && ($dateRange = $req["dateRange"])) {
            $time = explode(' - ', $dateRange);
            if ($time[0] == $time[1]) {
                $where[] = ["date", $time[0]];
            } else {
                $where[] = ['date', '>=', $time[0]];
                $where[] = ['date', '<=', $time[1]];
            }
        }
        if (!($agentId = $request->agentId)) $agentId = 'total';
        $data = self::query()->where($where)->orderBy("date", "desc");
        $count = $data->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();

        $type = $request->type;
        if ($type != 0 && !$type) {
            $type = 'total';
        }
        $provinces = allUserProvinces('total');
        if (isset($provinces[0])) unset($provinces[0]);
        foreach ($data as &$datum) {
            $json = json_decode($datum['json_data'], true);
            $json = isset($json[$agentId]) ? $json[$agentId] : [];
            if(!empty($json)){
                foreach ($json[$type] as $key => $value) {
                    $datum[$key] = $value;
                }
            }else{
                $modelName = ['total'];
                if($type == 1 || $type == 2){
                    $modelName = array_merge($modelName,batteryStockTitle(1));
                } elseif ($type == 0) {
                    $modelName = array_merge($modelName,batteryStockTitle(2));
                } elseif ($type == 4) {
                    $modelName = array_merge($modelName,batteryStockTitle(3));
                }
                foreach ($modelName as $value) {
                    $datum[$value] = 0;
                }
            }
        }
        return ["data" => $data, "count" => $count];
    }
}