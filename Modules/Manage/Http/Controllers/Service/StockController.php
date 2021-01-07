<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019-10-10 13:49
 */

namespace Modules\Manage\Http\Controllers\Service;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Manage\Models\Service\LeaseServiceStockLogInfo;
use Modules\Manage\Models\Service\ServiceStockLog;
use Modules\Manage\Services\ChartService;

class StockController
{
    protected $log;

    protected $stock;

    protected $chartService;

    public function __construct(LeaseServiceStockLogInfo $log, ServiceStockLog $stock, ChartService $chartService)
    {
        $this->log = $log;
        $this->stock = $stock;
        $this->chartService = $chartService;
    }

    //补货退库
    public function stockIndex()
    {
        $provinces = allUserProvinces("all");
        $provinces2 = allUserProvinces(0);
        $timeType = dateType();
        return view("manage::lease.service.stocks.supply", compact("provinces", "provinces2", "timeType"));
    }

    //退回
    public function returnIndex()
    {
        $provinces = allUserProvinces("all");
        $provinces2 = allUserProvinces(0);
        $timeType = dateType();
        return view("manage::lease.service.stocks.return", compact("provinces", "provinces2", "timeType"));
    }

    //回收
    public function recycleIndex()
    {
        $provinces = allUserProvinces("all");
        $provinces2 = allUserProvinces(0);
        $timeType = dateType();
        return view("manage::lease.service.stocks.recycle", compact("provinces", "provinces2", "timeType"));
    }

    //补货趋势
    public function supplyTrend(Request $request)
    {
        $data = $this->log->trend($request);
        if (!empty($data)) {
            return result("", 1, $data);
        }
        return result('', 0, []);
    }

    //补货分析
    public function supplyAnalysis(Request $request)
    {
        $data = $this->log->analysis($request);
        if (!empty($data)) {
            return result("", 1, $data);
        }
        return result('', 0, []);
    }

    //补货统计表
    public function supplyTable(Request $request)
    {
        try {
            $balanceData = $this->log->getList($request);
            return result("", 0, $balanceData["data"], $balanceData["count"]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //退回趋势
    public function returnTrend(Request $request)
    {
        $data = $this->log->returnTrend($request);
        if (!empty($data)) {
            return result("", 1, $data);
        }
        return result('', 0, []);
    }

    //退回分析
    public function returnAnalysis(Request $request)
    {
        $data = $this->log->returnAnalysis($request);
        if (!empty($data)) {
            return result("", 1, $data);
        }
        return result('', 0, []);
    }

    //退回统计表
    public function returnTable(Request $request)
    {
        try {
            $balanceData = $this->log->returnTable($request);
            return result("", 0, $balanceData["data"], $balanceData["count"]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //回收趋势
    public function recycleTrend(Request $request)
    {
        $data = $this->log->recycleTrend($request);
        if (!empty($data)) {
            return result("", 1, $data);
        }
        return result('', 0, []);
    }

    //回收分析
    public function recycleAnalysis(Request $request)
    {
        $data = $this->log->recycleAnalysis($request);
        if (!empty($data)) {
            return result("", 1, $data);
        }
        return result('', 0, []);
    }

    //回收统计表
    public function recycleTable(Request $request)
    {
        try {
            $balanceData = $this->log->recycleTable($request);
            return result("", 0, $balanceData["data"], $balanceData["count"]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //库存统计
    public function stock()
    {
        $provinces2 = allUserProvinces('total');
        return view("manage::lease.service.stocks.stock", compact("provinces", "provinces2", "timeType"));
    }

    //库存区域占比
    public function stockArea(Request $request)
    {
        $data = $this->stock->area($request);
        if (!empty($data)) {
            $provinces = allUserProvinces();
            return result("", 1, $this->chartService->histogramPileChart($data, $provinces));
        }
        return result('', 0, []);
    }

    //各区域库存统计
    public function areaList(Request $request)
    {
        $data = $this->stock->getList($request);
        if (!empty($data)) {
            return result("", 0, $data["data"], $data["count"]);
        }
        return result('', 0, []);
    }

    //电池型号占比
    public function battery(Request $request)
    {
        $data = $this->stock->battery($request);
        $xAxis = [];
        foreach ($data as $key => $datum) {
            if ($key != 'total') {
                $xAxis[$key] = $key;
            } else {
                $xAxis[0] = '总数';
            }
        }
        if (!empty($data)) {
            return result("", 1, $this->chartService->histogramPileChart($data, $xAxis));
        }
        return result('', 0, []);
    }

    //电池型号库存统计
    public function batteryList(Request $request)
    {
        $data = $this->stock->getBatteryList($request);
        if (!empty($data)) {
            return result("", 0, $data["data"], $data["count"]);
        }
        return result('', 0, []);
    }
}