<?php

namespace Modules\Manage\Http\Controllers\Lease;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseContractDateInfo;
use Modules\Manage\Models\Report\LeaseStartLog;

class NewUserLeaseController extends Controller
{
    protected $contractModel;
    protected $hourArr = ["00:00", "01:00", "02:00", "03:00", "04:00", "05:00", "06:00", "07:00", "08:00", "09:00",
        "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00", "21:00",
        "22:00", "23:00"];
    //有效合约状态
    protected $effectContractStatus = '2,3,4,5,7,8';

    public function __construct(LeaseContract $contractModel)
    {
        $this->contractModel = $contractModel;
    }

    //新用户租赁view
    public function leaseNewView()
    {
        $provinces = allUserProvinces("all");
        $provinces2 = allUserProvinces(0);
        $timeType = dateType();
        return view("manage::lease.report.lease.lease_new_chart", compact("provinces", "provinces2", "timeType"));
    }

    //新老用户租赁view
    public function index()
    {
        $provinces = allUserProvinces("all");
        $provinces2 = allUserProvinces(0);
        $timeType = timeType();
        return view("manage::lease.report.lease.lease_new_old_chart", compact("provinces", "provinces2", "timeType"));
    }

    //新用户各时点租赁数对比
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
            $defaultDayStr = "'" . implode("','", $defaultDay) . "'";
            $whereSql = "";
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $whereSql = " and province_id = " . $req["agentId"];
            }
            $effectContractStatus = $this->effectContractStatus;
            $sql = " SELECT count(id)as num,created_date,DATE_FORMAT(created_at, '%H') as hour,"
                . " DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as time FROM lease_contracts "
                . " where status in($effectContractStatus) $whereSql and created_date = user_register_at and "
                . " created_date in($defaultDayStr) GROUP BY time";
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
                        $series[$k]["data"][$item->hour * 1] = $item->num;
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

    //新用户各时点租赁金额对比
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
            $defaultDayStr = "'" . implode("','", $defaultDay) . "'";
            $whereSql = "";
            $effectContractStatus = $this->effectContractStatus;
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $whereSql = " and province_id = " . $req["agentId"];
            }
            $sql = " SELECT sum(rental_all) as rental_all,created_date,DATE_FORMAT(created_at, '%H') as hour,"
                . " DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as time FROM lease_contracts "
                . " where status in($effectContractStatus) $whereSql and created_date = user_register_at and "
                . " created_date in($defaultDayStr) GROUP BY time";
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
                        $series[$k]["data"][$item->hour * 1] = $item->rental_all;
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

    //新用户租赁趋势
    public function newLeaseTrend(Request $request)
    {
        try {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 day")),
                "end"   => date("Y-m-d")
            ];
            $data = LeaseContractDateInfo::leaseTrend($request, $defaultDay);
            $numData = [];
            $actuallyBegin = isset($data[0]) ? $data[0]["date"] : $defaultDay["begin"];
            foreach ($data as $datum) {
                $numData[] = $datum["today_num"];
            }

            $returnData = [
                "hourArr" => getDateRange($actuallyBegin, $defaultDay["end"]),
                "numData" => $numData,
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //新用户租赁统计
    public function newLeaseStatistics(Request $request)
    {
        try {
            $data = LeaseContractDateInfo::getList($request);
            return result("", 0, $data["data"], $data["count"]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //新老用户租赁统计表
    public function newOldStatistics(Request $request)
    {
        try {
            $data = LeaseContractDateInfo::newOldList($request);
            return result("", 0, $data["data"], $data["count"]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //新用户各时点租赁统计表
    public function dayStatistics(Request $request)
    {
        try {
            $req = $request->all();
            $defaultDay = date("Y-m-d");
            if (isset($req["day"]) && $req["day"]) {
                $defaultDay = $req["day"];
            }
            $whereSql = "";
            $effectContractStatus = $this->effectContractStatus;
            $select = " count(id) as num,sum(rental_all) as rental,DATE_FORMAT(created_at, '%H:00') as hour ";
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $whereSql = " and province_id = " . $req["agentId"];
            }
            //新老用户判断条件符号
            $type = $request->type == 1? '=':'!=';
            $data = LeaseContract::query()->selectRaw($select)
                ->whereRaw(" status in($effectContractStatus) $whereSql and created_date $type user_register_at ")
                ->where('created_date', $defaultDay)
                ->groupBy('hour')
                ->get()
                ->toArray();
            $result = [];
            foreach ($this->hourArr as $hour) {
                $result[$hour] = [
                    'hour'   => $hour,
                    'num'    => 0,
                    'rental' => 0,
                ];
                foreach ($data as $datum) {
                    if ($datum['hour'] == $hour) {
                        $result[$hour] = $datum;
                    }
                }
            }
            return result("", 0, $result);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //每日启动趋势
    public function startDay(Request $request)
    {
        try {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 day")),
                "end"   => date("Y-m-d")
            ];
            $data = LeaseStartLog::startDay($request, $defaultDay);
            $numData = [];
            $actuallyBegin = isset($data[0]) ? $data[0]["date"] : $defaultDay["begin"];
            foreach ($data as $datum) {
                $numData[] = $datum["num"];
            }

            $returnData = [
                "hourArr" => getDateRange($actuallyBegin, $defaultDay["end"]),
                "numData" => $numData,
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }
}
