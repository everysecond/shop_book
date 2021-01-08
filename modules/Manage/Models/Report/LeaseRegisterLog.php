<?php

namespace Modules\Manage\Models\Report;

use Modules\Manage\Models\Model;

class LeaseRegisterLog extends Model
{
    //当日
    const LOG_TYPE_ONE = 1;
    //累计
    const LOG_TYPE_TWO = 2;

    //解析每小时注册量
    public static function formatRegisterNum($data)
    {
        $nameArr = [
            "hour1", "hour2", "hour3", "hour4", "hour5", "hour6", "hour7", "hour8", "hour9", "hour10", "hour11", "hour12",
            "hour13", "hour14", "hour15", "hour16", "hour17", "hour18", "hour19", "hour20", "hour21", "hour22", "hour23", "hour24"
        ];
        foreach ($data as &$item) {
            $str = $item["register_num_str"];
            $numArr = explode(",", $str);
            foreach ($nameArr as $k => $name) {
                $item[$name] = isset($numArr[$k]) ? $numArr[$k] : 0;
            }
        }
        return $data;
    }

    //获取注册统计列表
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
        $data = self::select("date", "register_num_str", "total");
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $data->where("province_id", $req["agentId"]);
        }
        $data->where($where)->orderBy("date", "desc");
        $count = $data->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();
        $data = self::formatRegisterNum($data);
        return ["data" => $data, "count" => $count];
    }

    //获取注册统计汇总数据
    public static function getSumData($request)
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
        $data = self::selectRaw(" date,GROUP_CONCAT(total ORDER BY type) as 'num_str'");
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $data->where("province_id", $req["agentId"]);
        }
        $data->where($where)->groupBy("date")->orderBy("date", "desc");
        $count = $data->get()->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();
        $data = self::formatNumStr($data);
        return ["data" => $data, "count" => $count];
    }

    //每日注册趋势数据
    public static function registerDay($request, &$defaultDay)
    {
        $req = $request->all();
        if (isset($req["days"]) && $req["days"] != -1) {
            $days = $req["days"];
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-$days day")),
                "end"   => date("Y-m-d")
            ];
        } elseif (isset($req["days"]) && $req["days"] == -1 && isset($req["dateRange"]) && $req["dateRange"]) {
            $days = explode(" - ", $req["dateRange"]);
            $defaultDay = [
                "begin" => $days[0],
                "end"   => $days[1]
            ];
        }
        $data = self::selectRaw(" date,sum(total) as num ")->where("type", 1);
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $data->where("province_id", $req["agentId"]);
        }
        $data->whereBetween("date", $defaultDay)->groupBy("date")->orderBy("date");
        $data = $data->get()->toArray();
        return $data;
    }

    //解析当天及累计到当天总注册量
    public static function formatNumStr($data)
    {
        foreach ($data as &$item) {
            $str = $item["num_str"];
            $numArr = explode(",", $str);
            $item["day_num"] = isset($numArr[0]) ? $numArr[0] : 0;
            $item["total_num"] = isset($numArr[1]) ? $numArr[1] : 0;
        }
        return $data;
    }
}
