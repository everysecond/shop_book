<?php

namespace Modules\Manage\Http\Controllers\lease;

use \Exception;
use Illuminate\Http\Request;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Leaseprocess;
use Modules\Manage\Models\LeaseProcessHour;
use Modules\Manage\Repositories\Report\LeaseEventLogRepository;
use Modules\Manage\Services\ChartService;

class LoginRentController extends Controller
{
    protected $repository;
    protected $chartService;

    public function __construct(LeaseEventLogRepository $repository, ChartService $chartService)
    {
        $this->repository = $repository;
        $this->chartService = $chartService;
    }

    //登陆租赁view
    public function index()
    {
        $provinces = allUserProvinces();
        $timeType = dateType();
        return $this->view("lease.report.active_event", compact("provinces", "timeType"));
    }

    //登陆租赁趋势
    public function rentTrend(Request $request)
    {
        try {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 day")),
                "end"   => date("Y-m-d")
            ];

            $data = app(LeaseProcessHour::Class)->activeTrend($request, $defaultDay);
            $dateRange = array_column($data,'process_date');


            $legend = config("rentlogin.active_event");
            $hiddenLegend = [];
            foreach (config("rentlogin.active_event_display") as $url) {
                $hiddenLegend[$url] = false;
            }
            $days = [];
            foreach ($dateRange as $xAxi) {
                $days[] = date("m-d",strtotime($xAxi));
            }

            foreach ($legend as $code => $value) {
                $series[$code] = [
                    "name"       => $value,
                    "type"       => 'line',
                    "stack"      => $value,
                    "symbolSize" => 6,
                    "symbol"     => 'circle',
                    "data"       => array_column($data,$code)
                ];

            }

            $datas = [
                "legend"       => array_values($legend),
                "xAxis"        => $days,
                "series"       => array_values($series),
                "hiddenLegend" => $hiddenLegend
            ];


            return result("", 1, $datas);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //登陆租赁统计
    public function rentData(Request $request)
    {
        $defaultDay = [
            "begin" => date("Y-m-d", strtotime("-6 day")),
            "end"   => date("Y-m-d")
        ];

        $legend = config("rentlogin.active_event");
        $data = app(LeaseProcessHour::Class)->activeData($request, $defaultDay);

        $xAis  = $seriesData = [];

        foreach ($data as $key => $value) {
            $xAis[] = $legend[$key];
            $seriesData[] = $data[$key];

        }

        return result("", 1, ["xAxis" => array_values($xAis), "seriesData" => array_values($seriesData)]);
    }

    public function rentTable(Request $request) {
        $result = app(Leaseprocess::Class)->gettotalList($request);

        if (!empty($result)) {
            $data = $result['list'];
            $count = $result['count'];
            return result("", 0, $data, $count);
        }
        return result('暂无数据', 1, $result);

    }


}
