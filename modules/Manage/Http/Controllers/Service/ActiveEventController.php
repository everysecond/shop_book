<?php

namespace Modules\Manage\Http\Controllers\Service;

use \Exception;
use Illuminate\Http\Request;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Repositories\Report\LeaseEventLogRepository;
use Modules\Manage\Services\ChartService;

class ActiveEventController extends Controller
{
    protected $repository;
    protected $chartService;

    public function __construct(LeaseEventLogRepository $repository, ChartService $chartService)
    {
        $this->repository = $repository;
        $this->chartService = $chartService;
    }

    //活跃事件view
    public function index()
    {
        $provinces = allUserProvinces();
        $timeType = dateType();
        return $this->view("lease.service.user.active_event", compact("provinces", "timeType"));
    }

    //注册审核趋势
    public function activeTrend(Request $request)
    {
        try {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 day")),
                "end"   => date("Y-m-d")
            ];
            $data = $this->repository->activeTrend($request, $defaultDay);
            $actuallyBegin = isset($data[0]) ? $data[0]["date"] : $defaultDay["begin"];
            $dateRange = getDateRange($actuallyBegin, $defaultDay["end"]);
            $legend = config("global.active_event");
            unset($legend["account/login"]);
            $hiddenLegend = [];
            foreach (config("global.active_event_display") as $url) {
                $hiddenLegend[$url] = false;
            }
            return result("", 1, $this->chartService->lineActiveChart($data, $legend, $dateRange,$hiddenLegend));
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //活跃事件统计
    public function activeData(Request $request)
    {
        $defaultDay = [
            "begin" => date("Y-m-d", strtotime("-6 day")),
            "end"   => date("Y-m-d")
        ];
        $data = $this->repository->activeData($request, $defaultDay);
        $legend = config("global.active_event");
        unset($legend["account/login"]);
        return result("", 1, $this->chartService->histogramActiveChart($data, $legend));
    }

    //活跃事件统计表格
    public function activeTable(Request $request)
    {
        try {
            $data = $this->repository->activeList($request);
            return result("", 0, $data["data"], $data["count"]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }
}
