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
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Service\LeaseIncomeLog;
use Modules\Manage\Models\Service\LeaseServiceWithdraw;
use Modules\Manage\Models\Service\LeaseServiceWithdrawLog;
use Modules\Manage\Models\Service\LeaseServiceWithdrawRate;
use Modules\Manage\Repositories\Report\LeaseServiceRepository;
use Modules\Manage\Services\ChartService;

class WithdrawController extends Controller
{
    protected $repository;
    protected $chartService;
    protected $incomeModel;
    protected $leaseServiceWithdraw;
    protected $ageArr = ['7天','8-15天','16-30天','31-60天','60天以上'];
    public function __construct(LeaseServiceRepository $repository, ChartService $chartService,LeaseIncomeLog $log, LeaseServiceWithdraw $leaseServiceWithdraw)
    {
        $this->repository = $repository;
        $this->chartService = $chartService;
        $this->incomeModel = $log;
        $this->leaseServiceWithdraw = $leaseServiceWithdraw;
    }

    //余额
    public function index()
    {
        $provinces = allUserProvinces();
        $timeType = timeType();

        return $this->view("lease.service.income.withdraw", compact("provinces", "timeType"));
    }

        //各区域网点收益分布
    public function withdrawArea(Request $request)
    {
        $returnData['xAxis'] = ["0-50", "51-100", "101-150", "151-200", "201-250", "251-300", "301-350", "351-400", "401-450", "451-500", "500以上"];

        $province_id = request('province_id');
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
            $time =  selectTimeRange($time_type);

            if ($time){
                $where[] = ['created_at','>=',$time['start_time']];
                $where[] = ['created_at','<',$time['end_time']];

            }else{
                $time_s_d = explode(' - ',$renewal_date);
                $where[] = ['created_at','>=',$time_s_d[0]];
                $where[] = ['created_at','<=',$time_s_d[1]];
            }
        }

        if ($province_id) {
            $where['province_id'] = $province_id;
        }

        $where[] = ['id','>',0];


//        $list = LeaseService::selectRaw("count(lease_service_withdraws.id) as num,lease_services.id as service_id")
//            ->leftJoin('lease_service_withdraws', 'lease_service_withdraws.service_id', '=', 'lease_services.id')
//            ->where($where)->groupBy("lease_services.id")->pluck("num", "service_id")->toArray();

        $list = LeaseServiceWithdraw::selectRaw("amount,service_id")
            ->where($where)->get()->toArray();


        $i = [];
        for ($x=0; $x<=10; $x++) {
            $i[$x] = 0;
        }

        foreach ($list as $k=>$v ){
            if ($v['amount']>=0 && $v['amount']<=50){
                $i[0]++;

            }
            if ($v['amount']>=51 && $v['amount']<=100){
                $i[1]++;

            }
            if ($v['amount']>=101 && $v['amount']<=150){
                $i[2]++;

            }
            if ($v['amount']>=151 && $v['amount']<=200){
                $i[3]++;

            }
            if ($v['amount']>=201 && $v['amount']<=250){
                $i[4]++;

            }
            if ($v['amount']>=251 && $v['amount']<=300){
                $i[5]++;

            }
            if ($v['amount']>=301 && $v['amount']<=350){
                $i[6]++;

            }
            if ($v['amount']>=351 && $v['amount']<=400){
                $i[7]++;

            }
            if ($v['amount']>=401 && $v['amount']<=450){
                $i[8]++;

            }
            if ($v['amount']>=451 && $v['amount']<=500){
                $i[9]++;

            }
            if ($v['amount']>500){
                $i[10]++;
            }

        }

        $returnData['seriesData'] = $i;

        return result("", 1, $returnData);
    }

    //各区域网点收益统计
    public function withdrawTable(Request $request)
    {
        try {
            $balanceData = app(LeaseServiceWithdrawLog::class)->getList($request);


            return result("", 0, $balanceData["data"], $balanceData["count"]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception);
            return result($exception->getMessage(), -1);
        }
    }


    public function withdrawRate(Request $request)
    {
        try {

            $province_id = request('province_id');

            $sql = "elt(interval(rate_num,0,7,15,30,60),'7天','8-15天','16-30天','31-60天','60天以上')";
            $ageData = app(LeaseServiceWithdrawRate::class)->selectRaw($sql . " as rate_area,count(id) as num");

        if ($province_id) {
            $ageData->where("province_id", $province_id);
        }

        $data = $ageData->where("rate_num", ">", 0)->groupBy("rate_area")->pluck("num", "rate_area")->toArray();



            return result("", 1, $this->chartService->pieChartFormat($data, $this->ageArr));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception);
            return result($exception->getMessage(), -1);
        }
    }
}
