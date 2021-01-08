<?php

namespace Modules\Manage\Models\Report;

use Modules\Manage\Models\Model;

class LeaseBatteryLog extends Model
{
    //电池型号租赁
    public function batteryHistogramData($request, &$defaultDay)
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
        $data = self::selectRaw(" SUM(model_one) as one,SUM(model_two) as two,SUM(model_three) as three,"
            . "SUM(model_four) as four,SUM(model_five) five,SUM(model_six) as six,SUM(model_seven) as seven,"
            . "SUM(model_eight) as eight,SUM(model_nine) nine,SUM(other) as other");
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $data->where("province_id", $req["agentId"]);
        } else {
            $data->where("province_id", 0);
        }
        return $data->whereBetween("date", $defaultDay)->first();
    }

    //电池型号租赁比例
    public function batteryRate($request, &$defaultDay)
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
        $data = self::query()->selectRaw(" SUM(model_one) as 48V12A,SUM(model_two) as 48V20A,SUM(model_three) as 48V32A,"
            . "SUM(model_four) as 60V20A,SUM(model_five) 72V20A,SUM(model_six) as 48V45A,SUM(model_seven) as 60V32A,"
            . "SUM(model_eight) as 60V45A,SUM(model_nine) 72V32A,SUM(other) as 其它型号");
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $data->where("province_id", $req["agentId"]);
        } else {
            $data->where("province_id", 0);
        }
        return $data->whereBetween("date", $defaultDay)->first();
    }

    //获取租赁合约电池型号统计列表
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
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $where[] = ["province_id", $req["agentId"]];
        } else {
            $where[] = ["province_id", 0];
        }
        $data = self::where($where)->orderBy("date", "desc");
        $count = $data->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();
        return ["data" => $data, "count" => $count];
    }

    //获取租赁合约电池型号区域统计列表
    public function getModelList($request, $defaultDay)
    {
        $where = [];
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
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $where[] = ["province_id", $req["agentId"]];
        } else {
            $where[] = ["province_id", '>', 0];
        }
        $data = self::query()->selectRaw(" SUM(model_one) as model_one,SUM(model_two) as model_two,"
            . "SUM(model_three) as model_three,SUM(model_four) as model_four,SUM(model_five) model_five,"
            . "SUM(model_six) as model_six,SUM(model_seven) as model_seven,SUM(model_eight) as model_eight,"
            . "SUM(model_nine) model_nine,SUM(other) as other,Sum(total) as total,province_id")
            ->groupBy('province_id')
            ->where($where)
            ->whereBetween("date", $defaultDay)
            ->orderBy("province_id");
        $count = clone $data;
        $count = $count->get()->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();
        $provinceArr = allLeaseProvinces();
        foreach ($data as &$datum) {
            $datum['area'] = isset($provinceArr[$datum['province_id']]) ? $provinceArr[$datum['province_id']] : '';
        }
        return ["data" => $data, "count" => $count];
    }
}
