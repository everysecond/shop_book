<?php

namespace Modules\Manage\Http\Controllers\Service;

use Illuminate\Http\Request;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Report\LeaseStartLog;
use Modules\Manage\Models\Report\LeaseStartTerminalLog;

class StartLogController extends Controller
{
    protected $startLogModel;
    protected $hourArr = ["0:00", "1:00", "2:00", "3:00", "4:00", "5:00", "6:00", "7:00", "8:00",
        "9:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00",
        "19:00", "20:00", "21:00", "22:00", "23:00"];

    protected $appType = [
        "1" => "用户端IOS",
        "2" => "用户端安卓",
        "3" => "网点端IOS",
        "4" => "网点端安卓",
        "5" => "仓库端安卓",
        "6" => "物流端安卓",
        "7" => "工厂端安卓"
    ];

    public function __construct(LeaseStartLog $startLogModel)
    {
        $this->startLogModel = $startLogModel;
    }

    //启动数据view
    public function startChartView()
    {
        $timeType = dateType();
        return $this->view("lease.service.user.start_chart", compact("timeType"));
    }

    //用户每小时累计启动对比
    public function startHour(Request $request)
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
            $data = LeaseStartLog::select("date", "start_num_str", "total");
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $data->where("province_id", $req["agentId"]);
            }
            if (isset($req["type"]) && $req["type"]) {
                $data->where("type", $req["type"]);
            }
            $data->whereIn("date", $defaultDay);
            $data = $data->get()->toArray();
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
                    if ($item["date"] == $day) {
                        $numArr = explode(",", $item["start_num_str"]);
                        $numArr = array_chunk($numArr, 24)[0];
                        $series[$k]["data"] = $numArr;
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

    /**
     * 用户每小时启动统计
     * @param Request $request
     *        type 1 当天
     *        type 2 累计到当天
     * @return false|string
     */
    public function tableStartHour(Request $request)
    {
        try {
            $data = LeaseStartLog::getList($request);
            return result("", 0, $data["data"], $data["count"]);
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
            $data = LeaseStartLog::startDay($request, $defaultDay, LeaseStartLog::LOG_TYPE_THREE);
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

    //网点端启动次数分布数据
    public function startFrom(Request $request)
    {
        try {
            $req = $request->all();
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 day")),
                "end"   => date("Y-m-d")
            ];
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

            $modelData = LeaseStartTerminalLog::selectRaw("sum(web_ios_num) as web_ios_num,sum(web_android_num) as web_android_num");
            $modelData = $modelData->whereBetween("date", $defaultDay)->first();
            $modelDataFormat = [];
            $modelArr = [];
            if ($modelData) {
                if ($modelData->web_ios_num) {
                    $modelDataFormat[] = [
                        "value" => $modelData->web_ios_num,
                        "name"  => "网点端IOS"
                    ];
                    $modelArr[] = "网点端IOS";
                }
                if ($modelData->web_android_num) {
                    $modelDataFormat[] = [
                        "value" => $modelData->web_android_num,
                        "name"  => "网点端安卓"
                    ];
                    $modelArr[] = "网点端安卓";
                }
            }
            $returnData = [
                "modelArr"  => $modelArr,
                "modelData" => $modelDataFormat
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //启动统计
    public function startTotal(Request $request)
    {
        try {
            $data = LeaseStartLog::getSumData($request, LeaseStartLog::LOG_TYPE_THREE);
            return result("", 0, $data["data"], $data["count"]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

}
