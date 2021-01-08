<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/8 11:36
 */

namespace Modules\Manage\Http\Controllers\Service;

use \Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Lease\Models\BlService;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Service\LeaseIncomeLog;
use Modules\Manage\Models\Service\LeaseServiceBalanceLog;
use Modules\Manage\Repositories\Report\LeaseServiceRepository;
use Modules\Manage\Services\ChartService;

class IncomeController extends Controller
{
    protected $repository;
    protected $chartService;
    protected $incomeModel;

    public function __construct(LeaseServiceRepository $repository, ChartService $chartService, LeaseIncomeLog $log)
    {
        $this->repository = $repository;
        $this->chartService = $chartService;
        $this->incomeModel = $log;
    }

    //余额
    public function index()
    {
        $provinces = allUserProvinces();
        $timeType = dateType();
        return $this->view("lease.service.income.income", compact("provinces", "timeType"));
    }

    //各区域网点收益分布
    public function incomeArea(Request $request)
    {
        try {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 days")),
                "end"   => date("Y-m-d", strtotime("+1 day"))
            ];
            $balanceData = $this->repository->getIncomeArea($request, $defaultDay);
            $provinces = allUserProvinces();
            return result("", 1, $this->chartService->histogramPileChart($balanceData, $provinces));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception);
            return result($exception->getMessage(), -1);
        }
    }

    //各区域网点收益统计
    public function incomeTable(Request $request)
    {
        try {
            $balanceData = $this->incomeModel->getList($request);

            return result("", 0, $balanceData["data"], $balanceData["count"]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception);
            return result($exception->getMessage(), -1);
        }
    }

    //各区域网点收益分布
    public function incomeRank(Request $request)
    {
        try {
            $balanceData = LeaseServiceBalanceLog::query()->newQuery()
                ->selectRaw("sum(amount) as income,service_id")
                ->where("source", LeaseServiceBalanceLog::SOURCE_FOUR)
                ->groupBy("service_id")
                ->orderBy("income", "desc")
                ->limit(6)
                ->pluck("income", "service_id")
                ->toArray();
            $agentsMap = BlService::query()->whereIn('id', array_keys($balanceData))
                ->pluck('service_name', 'id')->toArray();
            foreach ($balanceData as $id => $num) {
                $replaceArea[$agentsMap[$id]] = $num;
            }
            asort($replaceArea);
            $categories = [];
            foreach ($replaceArea as $provinceName => $num) {
                $str = mb_strlen($provinceName) > 8 ? ' ...' : '';
                $categories[] = mb_substr($provinceName, 0, 8) . $str;
            }
            $data = [
                'categories' => $categories,
                'series'     => [['data' => array_values($replaceArea)]]
            ];
            return result("", 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //各区域网点收益分布
    public function incomeAreaRank(Request $request)
    {
        try {
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $areaData = $this->repository->getIncomeAreaRank($request);
            $categories = [];
            foreach ($areaData as $service) {
                if (isset($agentsMap[$service->province_id])) {
                    $categories[] = [
                        'jg'=>$agentsMap[$service->province_id],
                        'service_num'=>$service->service_num,
                        'income'=>$service->income
                    ];
                }
            }
            return response()->json(['data'=>$categories])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }
}
