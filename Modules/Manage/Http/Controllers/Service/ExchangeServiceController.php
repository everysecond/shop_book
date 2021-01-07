<?php

namespace Modules\Manage\Http\Controllers\Service;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Lease\Models\BlUser;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeasePayment;
use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Service\LeaseServiceExchange;


class ExchangeServiceController extends Controller
{
    protected $LeaseServiceExchange;


    public function __construct(LeaseServiceExchange $LeaseServiceExchange)
    {
        $this->LeaseServiceExchange = $LeaseServiceExchange;
    }

    //用户画像view
    public function exchangeService()
    {
        $provinces = allUserProvinces();
        $timeType = timeType();
        return view("manage::lease.service.rent.exchange_service", compact("provinces", "timeType"));
    }

    //用户年龄分布数据
    public function exchangeDistribution(Request $request)
    {

            $returnData['xAxis'] = ["0-10","11-20","21-30","31-40","41-50", "51-100", "101-150", "151-200", "201-250", "251-300", "301-350", "351-400", "401-450", "451-500"];

            $province_id = request('province_id');
            $renewal_date = request('renewal_date');

            $time_type = isset($request->time_type) ? $request->time_type :1;

            if ($time_type){
                $time =  selectTimeRange($time_type);
                if ($time){
                    $where[] = ['confirmed_at','>=',$time['start_time']];
                    $where[] = ['confirmed_at','<',$time['end_time']];

                }else{
                    $time_s_d = explode(' - ',$renewal_date);
                    $where[] = ['confirmed_at','>=',$time_s_d[0]];
                    $where[] = ['confirmed_at','<=',$time_s_d[1]];
                }
            }

             if ($province_id) {
                 $where['lease_service_exchanges.province_id'] = $province_id;
                 $where_s['province_id'] = $province_id;
                 $count = LeaseService::where($where_s)->count();
             }else{
                 $count = LeaseService::count();
             }

             $where['lease_service_exchanges.status'] = 4;


             $list = LeaseService::selectRaw("count(lease_service_exchanges.id) as num,lease_services.id as service_id")
                ->leftJoin('lease_service_exchanges', 'lease_service_exchanges.service_id', '=', 'lease_services.id')
                ->where($where)->groupBy("lease_services.id")->pluck("num", "service_id")->toArray();



             $i = [];
             for ($x=0; $x<=9; $x++) {
                $i[$x] = 0;
             }

             foreach ($list as $k=>$v ){
                 if ($v>=51 && $v<=100){
                     $i[1]++;
                     $count--;
                 }
                 if ($v>=101 && $v<=150){
                     $i[2]++;
                     $count--;
                 }
                 if ($v>=151 && $v<=200){
                     $i[3]++;
                     $count--;
                 }
                 if ($v>=201 && $v<=250){
                     $i[4]++;
                     $count--;
                 }
                 if ($v>=251 && $v<=300){
                     $i[5]++;
                     $count--;
                 }
                 if ($v>=301 && $v<=350){
                     $i[6]++;
                     $count--;
                 }
                 if ($v>=351 && $v<=400){
                     $i[7]++;
                     $count--;
                 }
                 if ($v>=401 && $v<=450){
                     $i[8]++;
                     $count--;
                 }
                 if ($v>=451 && $v<=500){
                     $i[9]++;
                     $count--;
                 }
                 if ($v>500){
                     $count--;
                 }

             }
            $i[0] = $count;
            $returnData['seriesData'] = $i;

            return result("", 1, $returnData);
    }



    //用户地区分布数据
    public function areaExchange(Request $request)
    {


            $time_type = isset($request->time_type) ? $request->time_type :1;
            $renewal_date = request('renewal_date');

            if ($time_type){
                $time =  selectTimeRange($time_type);
                if ($time){
                    $where[] = ['confirmed_at','>=',$time['start_time']];
                    $where[] = ['confirmed_at','<',$time['end_time']];

                }else{
                    $time_s_d = explode(' - ',$renewal_date);
                    $where[] = ['confirmed_at','>=',$time_s_d[0]];
                    $where[] = ['confirmed_at','<=',$time_s_d[1]];
                }
            }

            $where['status'] = 4;
            $areaData = LeaseServiceExchange::selectRaw("count(id) as num,province_id")
            ->where($where)
            ->groupBy("province_id")->pluck("num", "province_id")->toArray();

            $service_num =  LeaseService::selectRaw("count(id) as num,province_id")
                ->groupBy("province_id")->pluck("num", "province_id")->toArray();

            $max = 1;
            if (!empty($areaData)){
                foreach($areaData as $key =>$value){
                    if (empty($service_num[$key])) $service_num[$key] = 1;
                    $areaData[$key] = round($value/$service_num[$key],4);

                }
                $max = max($areaData);
            }
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $provincesArr = getAllProvincesName();



            $replaceArea = [];
            foreach ($areaData as $id => $num) {
                $replaceArea[$agentsMap[$id]] = $num;
            }
            $areaDataFormat = [];
            foreach ($provincesArr as $name) {
                $value = isset($replaceArea[$name]) ? $replaceArea[$name] : 0;
                $areaDataFormat[] = [
                    "value" => $value,
                    "name"  => $name
                ];
            }
            $returnData = [
                "max"      => $max,
                "min"      => 0,
                "areaData" => $areaDataFormat,
            ];
            return result("", 1,$returnData);

    }


}
