<?php

namespace Modules\Manage\Models\Report;

use Modules\Manage\Models\Model;

class LeaseContractDateInfo extends Model
{
    //新用户类型
    const TYPE_ONE = 1;
    //老用户类型
    const TYPE_TWO = 2;

    //每日注册趋势数据
    public static function leaseTrend($request, &$defaultDay)
    {
        $req = $request->all();
        $type = isset($req["type"]) ? $req["type"] : 1;
        if (isset($req["days"]) && $req["days"] != -1) {
            $days = $req["days"];
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-$days day")),
                "end"   => date("Y-m-d")
            ];
        } else if (isset($req["days"]) && $req["days"] == -1 && isset($req["dateRange"]) && $req["dateRange"]) {
            $days = explode(" - ", $req["dateRange"]);
            $defaultDay = [
                "begin" => $days[0],
                "end"   => $days[1]
            ];
        }
        $data = self::where("type", $type);
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $data->where("province_id", $req["agentId"]);
        }
        $data->whereBetween("date", $defaultDay)->groupBy("date")->orderBy("date");
        $data = $data->get()->toArray();
        return $data;
    }

    //获取租赁统计列表
    public static function getList($request)
    {
        $req = $request->all();
        $where = [
            "type" => isset($req["type"]) && $req["type"] ? $req["type"] : 1
        ];
        if (isset($req["dateRange"]) && ($dateRange = $req["dateRange"])) {
            $time = explode(' - ', $dateRange);
            if ($time[0] == $time[1]) {
                $where[] = ["date", $time[0]];
            } else {
                $where[] = ['date', '>=', $time[0]];
                $where[] = ['date', '<=', $time[1]];
            }
        }
        $data = self::where($where);
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $data->where("province_id", $req["agentId"]);
        }
        $data->orderBy("date", "desc");
        $count = $data->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();
        return ["data" => $data, "count" => $count];
    }

    //新老用户租赁统计表
    public static function newOldList($request)
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
        $select = " date,sum(if(type=1,today_num,0)) as new_num,sum(if(type=2,today_num,0)) as old_num," .
            "sum(if(type=1,today_rental,0)) as new_rental,sum(if(type=2,today_rental,0)) as old_rental ";
        $data = self::query()->selectRaw($select)->where($where);
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $data->where("province_id", $req["agentId"]);
        }
        $data->groupBy('date')->orderBy("date", "desc");
        $count = clone $data;
        $count = $count->get()->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();
        return ["data" => $data, "count" => $count];
    }
}
