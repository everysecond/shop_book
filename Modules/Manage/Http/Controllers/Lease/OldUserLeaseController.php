<?php

namespace Modules\Manage\Http\Controllers\Lease;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseContractDateInfo;

class OldUserLeaseController extends Controller
{
    protected $contractModel;
    protected $hourArr = ["0:00", "1:00", "2:00", "3:00", "4:00", "5:00", "6:00", "7:00", "8:00",
        "9:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00",
        "19:00", "20:00", "21:00", "22:00", "23:00"];

    //有效合约状态
    protected $effectContractStatus = '3,4,5,7,8';

    public function __construct(LeaseContract $contractModel)
    {
        $this->contractModel = $contractModel;
    }

    //启动数据view
    public function leaseOldView()
    {
        $provinces = allUserProvinces("all");
        $provinces2 = allUserProvinces(0);
        $timeType = dateType();
        return view("manage::lease.report.lease.lease_old_chart", compact("provinces","provinces2", "timeType"));
    }

    //老用户各时点租赁数对比
    public function leaseTimeHour(Request $request)
    {
        try {
            $req = $request->all();
            $defaultDay = [
                date("Y-m-d", strtotime("-1 day")),
                date("Y-m-d")
            ];
            if (isset($req["dayArr"]) && $req["dayArr"]) {
                $defaultDay = array_merge($req["dayArr"], $defaultDay);
            }
            $defaultDayStr = "'".implode("','",$defaultDay)."'";
            $whereSql = "";
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $whereSql = " and province_id = ".$req["agentId"] ;
            }
            $effectContractStatus = $this->effectContractStatus;
            $sql = " SELECT count(id)as num,created_date,DATE_FORMAT(created_at, '%H') as hour,"
                ." DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as time FROM lease_contracts "
                ." where status in($effectContractStatus) $whereSql and created_date != user_register_at and "
                ." created_date in($defaultDayStr) GROUP BY time";
            $data = DB::select($sql);
            $series = [];
            $defaultNumArr = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($defaultDay as $k => $day) {
                $series[$k] = [
                    "name"       => $day,
                    "type"       => 'line',
                    "stack"      => $day,
                    "symbolSize" => 6,
                    "symbol"     => 'circle',
                    "data"       => $defaultNumArr
                ];
                foreach ($data as $item) {
                    if ($item->created_date == $day) {
                        $series[$k]["data"][$item->hour*1] = $item->num;
                    }
                }
            }
            $returnData = [
                "days"    => $defaultDay,
                "hourArr" => $this->hourArr,
                "series"  => $series,
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //老用户各时点租赁金额对比
    public function leaseMoneyHour(Request $request)
    {
        try {
            $req = $request->all();
            $defaultDay = [
                date("Y-m-d", strtotime("-1 day")),
                date("Y-m-d")
            ];
            if (isset($req["dayArr"]) && $req["dayArr"]) {
                $defaultDay = array_merge($req["dayArr"], $defaultDay);
            }
            $defaultDayStr = "'".implode("','",$defaultDay)."'";
            $whereSql = "";
            $effectContractStatus = $this->effectContractStatus;
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $whereSql = " and province_id = ".$req["agentId"] ;
            }
            $sql = " SELECT sum(rental_all) as rental_all,created_date,DATE_FORMAT(created_at, '%H') as hour,"
                ." DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as time FROM lease_contracts "
                ." where status in($effectContractStatus) $whereSql and created_date != user_register_at and "
                ." created_date in($defaultDayStr) GROUP BY time";
            $data = DB::select($sql);
            $series = [];
            $defaultNumArr = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($defaultDay as $k => $day) {
                $series[$k] = [
                    "name"       => $day,
                    "type"       => 'line',
                    "stack"      => $day,
                    "symbolSize" => 6,
                    "symbol"     => 'circle',
                    "data"       => $defaultNumArr
                ];
                foreach ($data as $item) {
                    if ($item->created_date == $day) {
                        $series[$k]["data"][$item->hour*1] = $item->rental_all;
                    }
                }
            }
            $returnData = [
                "days"    => $defaultDay,
                "hourArr" => $this->hourArr,
                "series"  => $series,
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //老用户租赁趋势
    public function oldLeaseTrend(Request $request)
    {
        try {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 day")),
                "end"   => date("Y-m-d")
            ];
            $data = LeaseContractDateInfo::leaseTrend($request,$defaultDay);
            $numData = [];
            $actuallyBegin = isset($data[0])?$data[0]["date"]:$defaultDay["begin"];
            foreach ($data as $datum) {
                $numData[] = $datum["today_num"];
            }

            $returnData = [
                "hourArr" => getDateRange($actuallyBegin,$defaultDay["end"]),
                "numData"  => $numData,
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //老用户租赁统计
    public function oldLeaseStatistics(Request $request)
    {
        try {
            $data = LeaseContractDateInfo::getList($request);
            return result("", 0, $data["data"], $data["count"]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

}
