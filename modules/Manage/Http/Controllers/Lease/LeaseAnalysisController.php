<?php

namespace Modules\Manage\Http\Controllers\Lease;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Report\LeaseBatteryLog;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Services\ChartService;

class LeaseAnalysisController extends Controller
{
    protected $leaseContract;
    protected $chartService;

    public function __construct(LeaseContract $leaseContract, ChartService $chartService)
    {
        $this->leaseContract = $leaseContract;
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
        return view('manage::lease.report.lease.lease_analysis', compact("provinces", "timeType"));
    }

    /**
     * 租赁趋势柱状图数据
     * @param Request $request
     * @return false|string
     */
    public function trend(Request $request)
    {
        try {
            $defaultDay = $this->formatDateRange($request);
            $data = $this->leaseContract->trend($request, $defaultDay);

            $returnData = [
                "month"        => [],
                "lease_num"    => [],
                "lease_amount" => []
            ];
            foreach ($data as $datum) {
                $returnData['month'][] = dateFormat($datum['created_date'], 'm-d');
                $returnData['lease_num'][] = $datum['num'];
                $returnData['lease_amount'][] = $datum['rental_all'];
            }
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 租赁周期分布柱状图数据
     * @param Request $request
     * @return false|string
     */
    public function cycle(Request $request)
    {
        try {
            $defaultDay = $this->formatDateRange($request);
            $data = $this->leaseContract->cycle($request, $defaultDay);
            $provinceArr = allLeaseProvinces();
            $returnData = [
                "cycle"     => [],
                "lease_num" => []
            ];
            foreach ($data as $datum) {
                $returnData['cycle'][] = $datum['cycle'] % 12 == 0 ? $datum['cycle'] / 12 . '年' : $datum['cycle'] . '月';
                $returnData['lease_num'][] = $datum['num'];
            }
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 租赁区域分布柱状图数据
     * @param Request $request
     * @return false|string
     */
    public function area(Request $request)
    {
        try {
            $defaultDay = $this->formatDateRange($request);
            $data = $this->leaseContract->area($request, $defaultDay);
            $provinceArr = allLeaseProvinces();
            $returnData = [
                "province"     => [],
                "lease_num"    => [],
                "lease_amount" => []
            ];
            foreach ($data as $datum) {
                if (in_array($datum['province_id'], array_keys($provinceArr))) {
                    $returnData['province'][] = Arr::get($provinceArr, $datum['province_id']);
                    $returnData['lease_num'][] = $datum['num'];
                    $returnData['lease_amount'][] = $datum['rental_all'];
                }
            }
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
