<?php

namespace Modules\Manage\Models\Report;

use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\Model;

class LeaseContract extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    const STATUS_INVALID = 1;
    const STATUS_INVALID_TEXT = '未生效';

    const STATUS_WAIT = 2;
    const STATUS_WAIT_TEXT = '待生效';

    const STATUS_VALID = 3;
    const STATUS_VALID_TEXT = '已生效';

    const STATUS_RETIRED = 4;
    const STATUS_RETIRED_TEXT = '已退租';

    const STATUS_LOST = 5;
    const STATUS_LOST_TEXT = '已丢失';

    const STATUS_CANCELED = 6;
    const STATUS_CANCELED_TEXT = '已取消';

    const STATUS_RENEWED = 7;
    const STATUS_RENEWED_TEXT = '已续约';

    const STATUS_EXCHANGED = 8;
    const STATUS_EXCHANGED_TEXT = '已换租';

    const STATUS_INVALID_ARR = [
        self::STATUS_INVALID,
        self::STATUS_WAIT,
        self::STATUS_CANCELED
    ];

    //新租状态
    const PAYMENT_TYPE_ONE = 1;
    //续租状态
    const PAYMENT_TYPE_TWO = 2;
    //续约状态
    const PAYMENT_TYPE_THREE = 3;
    //换租状态
    const PAYMENT_STATUS_FOUR = 4;
    //不同型号换租
    const PAYMENT_STATUS_FIVE = 5;

    //待支付状态
    const PAYMENT_STATUS_ZERO = 0;
    //已支付状态
    const PAYMENT_STATUS_ONE = 1;
    //已取消状态
    const PAYMENT_STATUS_TWO = 2;

    //租赁趋势
    public function trend($request,$defaultDay)
    {
        $req = $request->all();
        $data = self::query()->selectRaw(" sum(rental_all) as rental_all,count(id) as num,created_date ")
            ->whereNotIn("status", self::STATUS_INVALID_ARR);
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $data->where("province_id", $req["agentId"]);
        }
        $data->whereBetween("created_at", $defaultDay)->groupBy("created_date")->orderBy("created_date");
        $data = $data->get()->toArray();
        return $data;
    }

    //租赁区域分布
    public function area($request,$defaultDay)
    {
        $req = $request->all();
        $data = self::query()->selectRaw(" sum(rental_all) as rental_all,count(id) as num,province_id ")
            ->whereNotIn("status", self::STATUS_INVALID_ARR)
            ->whereBetween("created_at", $defaultDay)
            ->where('province_id','>',0)
            ->groupBy("province_id")->orderBy("province_id");
        $data = $data->get()->toArray();
        return $data;
    }

    //租赁区域分布
    public function cycle($request,$defaultDay)
    {
        $req = $request->all();
        $data = self::query()->selectRaw(" count(id) as num,contract_term as cycle ")
            ->whereNotIn("status", self::STATUS_INVALID_ARR)
            ->whereBetween("created_at", $defaultDay)
            ->groupBy("contract_term")->orderBy("contract_term");
        $data = $data->get()->toArray();
        return $data;
    }

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
        $data = self::selectRaw(" model_name,count(id) as num ")
            ->where("status", self::STATUS_VALID);
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $data->where("province_id", $req["agentId"]);
        }
        $data->whereBetween("created_at", $defaultDay)->groupBy("model_id")->orderBy("model_name");
        $data = $data->get()->toArray();
        return $data;
    }

    //电池型号data
    public function scopeProvinceId($query, $provinceId, $field = "province_id,")
    {
        $where = ["status" => self::STATUS_VALID];
        $query = $query->selectRaw("created_date as date,count(id) as total,$field"
            . "sum(case when model_name='48V12A' THEN 1 ELSE 0 END) as model_one,"
            . "sum(case when model_name='48V20A' THEN 1 ELSE 0 END) as model_two,"
            . "sum(case when model_name='48V32A' THEN 1 ELSE 0 END) as model_three,"
            . "sum(case when model_name='60V20A' THEN 1 ELSE 0 END) as model_four,"
            . "sum(case when model_name='72V20A' THEN 1 ELSE 0 END) as model_five,"
            . "sum(case when model_name='48V45A' THEN 1 ELSE 0 END) as model_six,"
            . "sum(case when model_name='60V32A' THEN 1 ELSE 0 END) as model_seven,"
            . "sum(case when model_name='60V45A' THEN 1 ELSE 0 END) as model_eight,"
            . "sum(case when model_name='72V32A' THEN 1 ELSE 0 END) as model_nine,"
            . "sum(case when model_name not in ('48V12A','48V20A','48V32A','60V20A',"
            . "'72V20A','48V45A','60V32A','60V45A','72V32A') THEN 1 ELSE 0 END) as other");
        if ($provinceId) {
            $where["province_id"] = $provinceId;
        }
        return $query->where($where)->groupBy("created_date")->orderBy("created_date");
    }

    //电池型号租赁统计表
    public function getBatteryList($request)
    {
        $req = $request->all();
        $where = ["status" => self::STATUS_VALID];
        if (isset($req["dateRange"]) && ($dateRange = $req["dateRange"])) {
            $time = explode(' - ', $dateRange);
            if ($time[0] == $time[1]) {
                $where[] = ["created_date", $time[0]];
            } else {
                $where[] = ['created_date', '>=', $time[0]];
                $where[] = ['created_date', '<=', $time[1]];
            }
        }
        $data = self::selectRaw("created_date,count(id) as num,"
            . "sum(case when model_name='48V12A' THEN 1 ELSE 0 END) as 48V12A,"
            . "sum(case when model_name='48V20A' THEN 1 ELSE 0 END) as 48V20A,"
            . "sum(case when model_name='48V32A' THEN 1 ELSE 0 END) as 48V32A,"
            . "sum(case when model_name='60V20A' THEN 1 ELSE 0 END) as 60V20A,"
            . "sum(case when model_name='72V20A' THEN 1 ELSE 0 END) as 72V20A,"
            . "sum(case when model_name not in ('48V12A','48V20A','48V32A','60V20A','72V20A') THEN 1 ELSE 0 END) as other");
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $data->where("province_id", $req["agentId"]);
        }
        $data->where($where)->groupBy("created_date")->orderBy("created_date", "desc");
        $count = clone $data;
        $count = $count->get()->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();
        return ["data" => $data, "count" => $count];
    }


    //客户信息
    public function c_user()
    {
        return $this->hasOne(CrmUser::class, "user_id", "user_id")->whereCusType(CrmUser::CUS_TYPE_ONE);
    }


    //客户信息
    public function b_user()
    {
        return $this->hasOne(CrmUser::class, "user_id", "service_id")->whereCusType(CrmUser::CUS_TYPE_TWO);
    }
}
