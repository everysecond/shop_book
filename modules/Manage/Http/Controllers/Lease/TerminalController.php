<?php

namespace Modules\Manage\Http\Controllers\Lease;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Manage\Models\Report\LeaseAppDownLog;
use Modules\Manage\Services\ChartService;

class TerminalController extends Controller
{
    protected $model;
    protected $chartService;

    public function __construct(LeaseAppDownLog $leaseAppDownLog, ChartService $chartService)
    {
        $this->model = $leaseAppDownLog;
        $this->chartService = $chartService;
    }

    public function terminalView()
    {
        $timeType = dateType();
        $channelArr = LeaseAppDownLog::CHANNEL_ARR;
        return view("manage::lease.report.user.terminal_chart", compact("timeType", 'channelArr'));
    }

    //下载趋势
    public function trend(Request $request)
    {
        try {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 day")),
                "end"   => date("Y-m-d")
            ];
            $data = $this->model->trend($request, $defaultDay);
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
            Log::error("下载趋势报错:{$exception->getMessage()}");
            return result($exception->getMessage(), -1);
        }
    }

    //下载量排行
    public function terminalSort(Request $request)
    {
        try {
            $data = $this->model->getSortProfile($request);
            return result("", 1, ["xAxis" => array_keys($data), "seriesData" => array_values($data)]);
        } catch (\Exception $exception) {
            Log::error("下载量排行报错:{$exception->getMessage()}");
            return result($exception->getMessage(), -1);
        }
    }

    //终端下载统计
    public function terminalTable(Request $request)
    {
        try {
            $balanceData = $this->model->getList($request);
            return result("", 0, $balanceData["data"], $balanceData["count"]);
        } catch (\Exception $exception) {
            Log::error("终端下载统计报错:{$exception->getMessage()}");
            return result($exception->getMessage(), -1);
        }
    }
}
