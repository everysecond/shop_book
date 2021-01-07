<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/8 9:02
 */

namespace Modules\Manage\Http\Controllers\Service;

use \Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Repositories\Report\LeaseServiceRepository;
use Modules\Manage\Services\ChartService;

class BalanceController extends Controller
{
    protected $repository;
    protected $chartService;
    protected $ageArr = ["30岁以下", "31-40岁", "41-50岁", "50岁以上"];
    protected $balanceField = "0,100,200,300,400,500,600,700,800,900,1000";
    protected $balanceFieldName =
        "'0-100','100-200','200-300','300-400','400-500','500-600','600-700','700-800','800-900','900-1000','1000以上'";
    protected $balanceFieldArr =
        ['0-100', '100-200', '200-300', '300-400', '400-500', '500-600', '600-700', '700-800', '800-900', '900-1000', '1000以上'];

    public function __construct(LeaseServiceRepository $repository, ChartService $chartService)
    {
        $this->repository = $repository;
        $this->chartService = $chartService;
    }

    //余额
    public function index()
    {
        $provinces = allUserProvinces();
        return $this->view("lease.service.income.balance", compact("provinces"));
    }

    //用户余额数据
    public function balance(Request $request)
    {
        try {
            $balanceData = $this->repository->getBalanceProfile($request->agentId, $this->balanceField, $this->balanceFieldName);
            return result("", 1, $this->chartService->histogramChart($balanceData, $this->balanceFieldArr));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception);
            return result($exception->getMessage(), -1);
        }
    }

    //用户余额数据
    public function balanceArea()
    {
        try {
            $balanceData = $this->repository->getBalanceArea();
            $provinces = allUserProvinces();
            return result("", 1, $this->chartService->histogramPileChart($balanceData, $provinces));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception);
            return result($exception->getMessage(), -1);
        }
    }
}
