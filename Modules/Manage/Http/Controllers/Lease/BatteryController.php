<?php

namespace Modules\Manage\Http\Controllers\Lease;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Manage\Models\Report\LeaseBatteryLog;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Services\ChartService;

class BatteryController extends Controller
{
    protected $batteryLog;
    protected $chartService;

    public function __construct(LeaseBatteryLog $leaseBatteryLog, ChartService $chartService)
    {
        $this->batteryLog = $leaseBatteryLog;
        $this->chartService = $chartService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $provinces = allUserProvinces("all");
        $timeType = dateType();
        return view('manage::lease.report.lease.battery_model', compact("provinces", "timeType"));
    }

    /**
     * 电池型号柱状图数据
     * @param Request $request
     * @return false|string
     */
    public function batteryHistogram(Request $request)
    {
        try {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 day")),
                "end"   => date("Y-m-d")
            ];
            $data = $this->batteryLog->batteryHistogramData($request, $defaultDay);
            $xAxis = ["48V12A", "48V20A", "48V32A", "48V45A", "60V20A", "60V32A", "60V45A", "72V20A", "72V32A", "其它型号"];
            $seriesData = [0, 0, 0, 0, 0, 0];
            if ($data) {
                $seriesData = [$data->one, $data->two, $data->three, $data->six, $data->four, $data->seven, $data->eight, $data->five, $data->nine, $data->other];
            }

            $returnData = [
                "xAxis"      => $xAxis,
                "seriesData" => $seriesData
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //用户年龄分布数据
    public function batteryRate(Request $request)
    {
        try {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 day")),
                "end"   => date("Y-m-d")
            ];
            $ageData = $this->batteryLog->batteryRate($request, $defaultDay);
            $ageData = $ageData ? $ageData->toArray() : [];
            $arr = ['48V12A', '48V20A', '48V32A', '48V45A', '60V20A', '60V32A', '60V45A', '72V20A', '72V32A', '其它型号'];
            return result("", 1, $this->chartService->pieChartFormat($ageData, $arr));
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 电池型号数统计
     * @param Request $request
     * @return false|string
     */
    public function batteryTable(Request $request)
    {
        try {
            $data = $this->batteryLog->getList($request);
            return result("", 0, $data["data"], $data["count"]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 电池型号数统计
     * @param Request $request
     * @return false|string
     */
    public function modelTable(Request $request)
    {
        try {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 day")),
                "end"   => date("Y-m-d")
            ];
            $data = $this->batteryLog->getModelList($request, $defaultDay);
            return result("", 0, $data["data"], $data["count"]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //电池报失柱状图数据
    public function batteryHistogramBak(Request $request)
    {
        $defaultDay = [
            "begin" => date("Y-m-d", strtotime("-6 day")),
            "end"   => date("Y-m-d")
        ];
        $data = LeaseContract::batteryHistogramData($request, $defaultDay);
        $xAxis = [];
        $seriesData = [];
        foreach ($data as $datum) {
            $xAxis[] = $datum["model_name"];
            $seriesData[] = $datum["num"];
        }

        $returnData = [
            "xAxis"      => $xAxis,
            "seriesData" => $seriesData
        ];
        return result("", 1, $returnData);
    }
}
