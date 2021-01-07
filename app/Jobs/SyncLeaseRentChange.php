<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlFlow;
use Modules\Lease\Models\BlLeaseExchange;
use Modules\Manage\Models\LeaseChangeReason;
use Modules\Manage\Models\LeaseInsurance;
use Modules\Manage\Models\LeaseRentChange;
use Modules\Manage\Models\Report\LeaseRegisterLog;
use Carbon\Carbon;

class SyncLeaseRentChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //最多运行5次
    public $tries = 5;

    /**
     * 该处处理成数组是为了兼容其他地方的使用
     * Create a new job instance.
     * @param array $user eg: ['email' => '121@123.com', 'name' => 'job']
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $provinces = allLeaseProvinces();
            $provinces[0] = '全部区域';
            $time_1 = selectTimeRange(8);
            //昨日换租
            $list =BlLeaseExchange::select("confirmed_at","contract_expired_at","province_id","reason")
                ->leftJoin('bl_lease_services', 'bl_lease_exchanges.id', '=', 'bl_lease_services.serviceable_id')
                ->leftJoin('bl_lease_contracts', 'bl_lease_contracts.id', '=', 'bl_lease_services.contract_id')
                ->where('bl_lease_services.serviceable_type', '=', 'App\Models\LeaseExchangeModel')
                ->where('bl_lease_exchanges.confirmed_at','>',Carbon::yesterday())
                ->where('bl_lease_exchanges.status','=',4)
                ->where('bl_lease_exchanges.confirmed_at','<',Carbon::today())
                ->get()->toArray();

            foreach ($provinces as $k =>$v){
                $rent_change_num[$k] = 0;
                $sign_in[$k] = 0;
                $register_num[$k] = 0;
                $rent_num[$k] = 0;
                $reason_1[$k] = 0;
                $reason_2[$k] = 0;
                $reason_3[$k] = 0;
            }

            //登录数
            $sign_in = BlFlow::selectRaw("count(distinct user_id) as num,province_id")
                ->leftJoin('bl_users', 'bl_flows.user_id', '=', 'bl_users.id')
                ->groupby('province_id')
                ->where('bl_flows.user_id','>',0)
                ->where('bl_flows.created_at','>',Carbon::yesterday())
                ->where('bl_flows.created_at','<',Carbon::today())
                ->pluck('num','province_id')->toArray();


            //登录总数
            $sign_in_total= 0;
            foreach ($sign_in as $k1 =>$v1){
                 $sign_in_total += $v1;
            }


            $time = date('Y-m-d',strtotime("-1 days"));
            //注册数
            $register_num = LeaseRegisterLog::selectRaw("total,province_id")
                ->where('date','=',$time)
                ->where('type','=',1)
                ->pluck('total','province_id')->toArray();

            //租赁数
            $rent_num = LeaseInsurance::selectRaw("rent_num as today_num,province_id")
                ->where('rent_date','>=',Carbon::yesterday())
                ->where('rent_date','<=',Carbon::today())
                ->groupby('province_id')
                ->pluck('today_num','province_id')->toArray();

            if (!empty($list)){
                //换租数
                foreach ($provinces as $k=>$v){
                    foreach ($list as $key=>$value){
                        if ($k==$value['province_id']){
                            ++$rent_change_num[$k];
                            ++$rent_change_num[0];
                        }

                        if ($k==$value['province_id']&& $value['reason'] == 1){
                            ++$reason_1[$k];
                            ++$reason_1[0];
                        }

                        if ($k==$value['province_id']&& $value['reason'] == 2){
                            ++$reason_2[$k];
                            ++$reason_2[0];
                        }

                        if ($k==$value['province_id']&& $value['reason'] == 3){
                            ++$reason_3[$k];
                            ++$reason_3[0];
                        }
                    }
                }
            }

            $data = $change_reason_1 =  $change_reason_2 =$change_reason_3 =[];
            foreach ($provinces as $k2=>$v3){
                $data[$k2]['rent_change_date'] = $time_1['start_time'];
                $data[$k2]['created_at'] = time();
                $data[$k2]['province_id'] = $k2;
                $data[$k2]['rent_change_num'] = $rent_change_num[$k2];
                $data[$k2]['type'] = 1;
                if (empty($register_num[$k2])) $register_num[$k2] = 0;
                $data[$k2]['register_num'] = $register_num[$k2];
                if (empty($rent_num[$k2])) $rent_num[$k2] = 0;
                $data[$k2]['rent_num'] = $rent_num[$k2];
                if (empty($sign_in[$k2])) $sign_in[$k2] = 0;
                $data[$k2]['sign_in_num'] = $sign_in[$k2];
                if ($k2 == 0){
                    $data[$k2]['sign_in_num'] = $sign_in_total;
                }

            }
            sort($data);
            //先删除再覆盖
            LeaseRentChange::where("rent_change_date","=",$time)->delete();
            //换组插入数据
            LeaseRentChange::insert($data);


            foreach($reason_1 as $k1=>$v1){
                $change_reason_1[$k1]['type'] = 2;
                $change_reason_1[$k1]['province_id'] = $k1;
                $change_reason_1[$k1]['change_reason_date'] = Carbon::yesterday();
                $change_reason_1[$k1]['created_at'] = time();
                $change_reason_1[$k1]['change_reason_num'] = $reason_1[$k1];
            }

            foreach($reason_2 as $k2=>$v2){
                $change_reason_2[$k2]['type'] = 1;
                $change_reason_2[$k2]['province_id'] = $k2;
                $change_reason_2[$k2]['change_reason_date'] = Carbon::yesterday();
                $change_reason_2[$k2]['created_at'] = time();
                $change_reason_2[$k2]['change_reason_num'] = $reason_2[$k2];
            }

            foreach($reason_3 as $k3=>$v3){
                $change_reason_3[$k3]['type'] = 3;
                $change_reason_3[$k3]['province_id'] = $k3;
                $change_reason_3[$k3]['change_reason_date'] = Carbon::yesterday();
                $change_reason_3[$k3]['created_at'] = time();
                $change_reason_3[$k3]['change_reason_num'] = $reason_3[$k3];
            }

            //先删除再覆盖
            LeaseChangeReason::where("change_reason_date","=",$time)->delete();
            //插入换组原因数据
            LeaseChangeReason::insert($change_reason_1);
            LeaseChangeReason::insert($change_reason_2);
            LeaseChangeReason::insert($change_reason_3);


            systemLog("同步租点换租统计任务成功!");


        } catch (\Exception $e) {
            \Log::error("同步租点换租统计失败: {$e->getMessage()}");
            systemLog("同步租点换租统计失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步租点换租统计失败：{$exception->getMessage()}");
        systemLog("同步租点换租统计失败: 详情请见laravel-log");
    }
}
