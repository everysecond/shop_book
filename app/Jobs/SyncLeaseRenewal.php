<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlLeasePayment;
use Modules\Lease\Models\BlLeaseContract;
use Modules\Manage\Models\LeaseRenewal;
use Carbon\Carbon;

class SyncLeaseRenewal implements ShouldQueue
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

            foreach ($provinces as $k =>$v){
                $renewal_month_total[$k] = 0;
                $renewal_user_num[$k] = [];
                $renewal_num_total[$k] = 0;
                $renewal_amount[$k] = 0;
                $advance_renewal[$k] = 0;
                $expire_renewal_num[$k] = 0;
                $overtime_one_three_renewal_num[$k] = 0;
                $overtime_four_seven_renewal_num[$k] = 0;
                $overtime_eight_ten_renewal_num[$k] = 0;
                $overtime_ten_renewal_num[$k] = 0;
                $overtime_ten_thirty_renewal_num[$k] = 0;
            }

            //查询昨日新增续约订单
            $time_1 = selectTimeStrtotime(1);
            $time_1_3 = selectTimeStrtotime(2);
            $time_4_7 = selectTimeStrtotime(3);
            $time_8_10 = selectTimeStrtotime(4);
            $time_1_10 = selectTimeStrtotime(5);
            $time_11_30 = selectTimeStrtotime(6);

            $time = selectTimeRange(8);
            $list = [];
            $list = BlLeasePayment::where("payed_at","<=",$time['end_time'])
                ->where("payed_at",">",$time['start_time'])->where("status","=",1)->whereIn("type",array('2','3'))
             ->get()->toArray();

            $prev_renewal_contract_id = $prev_renewal_list = $prev_renewal_contract_list = $prev_renewal_id = $renewal_contract_id = $renewal_id = [];
            foreach ($list as $key=>$value){
                if ($value['type'] == 2) {
                    $renewal_id[] = $value['contract_id'];//续租
                }
                if ($value['type']== 3){
                    $renewal_contract_id[] = $value['contract_id'];//续约
                }
            }

            $prev_renewal_list = BlLeaseContract::whereIn("id",$renewal_id)->get()->toArray();

            $renewal_contract_list = BlLeaseContract::whereIn("id",$renewal_contract_id)->get()->toArray();

            $prev_renewal_contract_id = array_column($renewal_contract_list,'prev_id');

            $prev_renewal_contract_list = BlLeaseContract::whereIn("id",$prev_renewal_contract_id)->get()->toArray();

            if (!empty($renewal_contract_list)){
                foreach ($renewal_contract_list as $k1=>$v1){
                    if ($v1['lease_unit'] == "year") {
                        $v1['lease_unit'] = 12;
                    }else{
                        $v1['lease_unit'] = 1;
                    }
                    $renewal_month_total[0]+= $v1['lease_unit']*$v1['lease_term'];
                    foreach ($provinces as $k2 =>$v2){
                        if ($k2 == $v1['province_id']) {
                            $renewal_month_total[$k2]+= $v1['lease_unit']*$v1['lease_term'];
                        }
                    }
                }
            }

            //统计对应时间次数
            if (!empty($prev_renewal_contract_list)){
                foreach ($prev_renewal_contract_list as $k1=>$v1){
                    ++$renewal_num_total[0];

                    if (strtotime($v1['contract_expired_at'])> $time_1['end_time']){
                        ++$advance_renewal[0];
                    }


                    if(strtotime($v1['contract_expired_at'])<=$time_1['end_time']&&strtotime($v1['contract_expired_at'])>$time_1['start_time']){
                        ++$expire_renewal_num[0];
                    }

                    if(strtotime($v1['contract_expired_at'])<=$time_1_3['end_time']&&strtotime($v1['contract_expired_at'])>$time_1_3['start_time']){
                        ++$overtime_one_three_renewal_num[0];
                    }

                    if(strtotime($v1['contract_expired_at'])<=$time_4_7['end_time']&&strtotime($v1['contract_expired_at'])>$time_4_7['start_time']){
                        ++$overtime_four_seven_renewal_num[0];
                    }

                    if(strtotime($v1['contract_expired_at'])<=$time_8_10['end_time']&&strtotime($v1['contract_expired_at'])>$time_8_10['start_time']){
                        ++$overtime_eight_ten_renewal_num[0];
                    }

                    if(strtotime($v1['contract_expired_at'])<=$time_1_10['end_time']&&strtotime($v1['contract_expired_at'])>$time_1_10['start_time']){
                        ++$overtime_ten_renewal_num[0];
                    }

                    if(strtotime($v1['contract_expired_at'])<=$time_11_30['end_time']&&strtotime($v1['contract_expired_at'])>$time_11_30['start_time']){
                        ++$overtime_ten_thirty_renewal_num[0];
                    }

                    foreach ($provinces as $k2 =>$v2){
                        if ($k2==$v1['province_id']){
                            ++$renewal_num_total[$k2];

                        }
                        if (strtotime($v1['contract_expired_at'])>$time_1['end_time'] && $k2==$v1['province_id']){

                            ++$advance_renewal[$k2];
                        }

                        if(strtotime($v1['contract_expired_at'])<=$time_1['end_time'] && strtotime($v1['contract_expired_at'])>$time_1['start_time'] && $k2==$v1['province_id']){
                            ++$expire_renewal_num[$k2];
                        }

                        if(strtotime($v1['contract_expired_at'])<=$time_1_3['end_time']&&strtotime($v1['contract_expired_at'])>$time_1_3['start_time'] && $k2==$v1['province_id']){
                            ++$overtime_one_three_renewal_num[$k2];
                        }

                        if(strtotime($v1['contract_expired_at'])<=$time_4_7['end_time']&&strtotime($v1['contract_expired_at'])>$time_4_7['start_time'] && $k2==$v1['province_id']){
                            ++$overtime_four_seven_renewal_num[$k2];
                        }

                        if(strtotime($v1['contract_expired_at'])<=$time_8_10['end_time']&&strtotime($v1['contract_expired_at'])>$time_8_10['start_time'] && $k2==$v1['province_id']){
                            ++$overtime_eight_ten_renewal_num[$k2];
                        }

                        if(strtotime($v1['contract_expired_at'])<=$time_1_10['end_time']&&strtotime($v1['contract_expired_at'])>$time_1_10['start_time'] && $k2==$v1['province_id']){
                            ++$overtime_ten_renewal_num[$k2];
                        }

                        if(strtotime($v1['contract_expired_at'])<=$time_11_30['end_time']&&strtotime($v1['contract_expired_at'])>$time_11_30['start_time'] && $k2==$v1['province_id']){
                            ++$overtime_ten_thirty_renewal_num[$k2];
                        }

                    }

                }
            }

            if (!empty($prev_renewal_list)) {
                foreach ($prev_renewal_list as $k1 => $v1) {
                    if ($v1['lease_unit'] == "year") {
                        $v1['lease_unit'] = 12;
                    }else{
                        $v1['lease_unit'] = 1;
                    }
                    $renewal_month_total[0]+= $v1['lease_unit']*$v1['lease_term'];
                    ++$renewal_num_total[0];
                    if (strtotime($v1['lease_expired_at']) > $time_1['end_time']) {
                        ++$advance_renewal[0];
                    }

                    if (strtotime($v1['lease_expired_at']) <= $time_1['end_time'] && strtotime($v1['lease_expired_at']) > $time_1['start_time']) {
                        ++$expire_renewal_num[0];
                    }

                    if (strtotime($v1['lease_expired_at']) <= $time_1_3['end_time'] && strtotime($v1['lease_expired_at']) > $time_1_3['start_time']) {
                        ++$overtime_one_three_renewal_num[0];
                    }

                    if (strtotime($v1['lease_expired_at']) <= $time_4_7['end_time'] && strtotime($v1['lease_expired_at']) > $time_4_7['start_time']) {
                        ++$overtime_four_seven_renewal_num[0];
                    }

                    if (strtotime($v1['lease_expired_at']) <= $time_8_10['end_time'] && strtotime($v1['lease_expired_at']) > $time_8_10['start_time']) {
                        ++$overtime_eight_ten_renewal_num[0];
                    }

                    if (strtotime($v1['lease_expired_at']) <= $time_1_10['end_time'] && strtotime($v1['lease_expired_at']) > $time_1_10['start_time']) {
                        ++$overtime_ten_renewal_num[0];
                    }

                    if (strtotime($v1['lease_expired_at']) <= $time_11_30['end_time'] && strtotime($v1['lease_expired_at']) > $time_11_30['start_time']) {
                        ++$overtime_ten_thirty_renewal_num[0];
                    }

                    foreach ($provinces as $k2 => $v2) {
                        if ($k2 == $v1['province_id']) {
                            ++$renewal_num_total[$k2];
                            $renewal_month_total[$k2]+= $v1['lease_unit']*$v1['lease_term'];
                        }

                        if (strtotime($v1['lease_expired_at']) > $time_1['end_time'] && $k2 == $v1['province_id']) {
                            ++$advance_renewal[$k2];
                        }

                        if (strtotime($v1['lease_expired_at']) <= $time_1['end_time'] && strtotime($v1['lease_expired_at']) > $time_1['start_time'] && $k2 == $v1['province_id']) {
                            ++$expire_renewal_num[$k2];
                        }

                        if (strtotime($v1['lease_expired_at']) <= $time_1_3['end_time'] && strtotime($v1['lease_expired_at']) > $time_1_3['start_time'] && $k2 == $v1['province_id']) {
                            ++$overtime_one_three_renewal_num[$k2];
                        }

                        if (strtotime($v1['lease_expired_at']) <= $time_4_7['end_time'] && strtotime($v1['lease_expired_at']) > $time_4_7['start_time'] && $k2 == $v1['province_id']) {
                            ++$overtime_four_seven_renewal_num[$k2];
                        }

                        if (strtotime($v1['lease_expired_at']) <= $time_8_10['end_time'] && strtotime($v1['lease_expired_at']) > $time_8_10['start_time'] && $k2 == $v1['province_id']) {
                            ++$overtime_eight_ten_renewal_num[$k2];
                        }

                        if (strtotime($v1['lease_expired_at']) <= $time_1_10['end_time'] && strtotime($v1['lease_expired_at']) > $time_1_10['start_time'] && $k2 == $v1['province_id']) {
                            ++$overtime_ten_renewal_num[$k2];
                        }

                        if (strtotime($v1['lease_expired_at']) <= $time_11_30['end_time'] && strtotime($v1['lease_expired_at']) > $time_11_30['start_time'] && $k2 == $v1['province_id']) {
                            ++$overtime_ten_thirty_renewal_num[$k2];
                        }

                    }

                }
            }


            //拼接数组
            foreach ($provinces as $k2 =>$v2){
                if (!empty($list)&&(!empty($prev_renewal_contract_list) || !empty($prev_renewal_list))) {
                    foreach ($list as $k3 => $v3) {
                        $data[$k2]['renewal_date'] = $time['start_time'];
                        $data[$k2]['created_at'] = time();
                        $data[$k2]['province_id'] = $k2;
                        $data[$k2]['renewal_num'] = $renewal_num_total[$k2];
                        $data[$k2]['advance_renewal'] = $advance_renewal[$k2];
                        $data[$k2]['expire_renewal_num'] = $expire_renewal_num[$k2];
                        $data[$k2]['overtime_one_three_renewal_num'] = $overtime_one_three_renewal_num[$k2];
                        $data[$k2]['overtime_four_seven_renewal_num'] = $overtime_four_seven_renewal_num[$k2];
                        $data[$k2]['overtime_eight_ten_renewal_num'] = $overtime_eight_ten_renewal_num[$k2];
                        $data[$k2]['overtime_ten_renewal_num'] = $overtime_ten_renewal_num[$k2];
                        $data[$k2]['overtime_ten_thirty_renewal_num'] = $overtime_ten_thirty_renewal_num[$k2];
                        $data[$k2]['type'] = 1;
                        $data[$k2]['renewal_month_total'] = $renewal_month_total[$k2];
                        $data[$k2]['renewal_amount'] = $renewal_amount[$k2];
                        if ($k2 == $v3['province_id']) {
                            $renewal_amount[$k2] += $v3['rental'];
                            $data[$k2]['renewal_amount'] = $renewal_amount[$k2];
                            $renewal_amount[0] += $v3['rental'];
                            $data[0]['renewal_amount'] = $renewal_amount[0];

                            $renewal_user_num[$k2][] = $v3['user_id'];
                            $renewal_user_num[0][] = $v3['user_id'];
                        }
                    }

                }else{
                    $data[$k2]['renewal_date'] = $time['start_time'];
                    $data[$k2]['created_at'] = time();
                    $data[$k2]['province_id'] = $k2;
                    $data[$k2]['renewal_num'] = 0;
                    $data[$k2]['advance_renewal'] = 0;
                    $data[$k2]['expire_renewal_num'] = 0;
                    $data[$k2]['overtime_one_three_renewal_num'] = 0;
                    $data[$k2]['overtime_four_seven_renewal_num'] = 0;
                    $data[$k2]['overtime_eight_ten_renewal_num'] = 0;
                    $data[$k2]['overtime_ten_renewal_num'] = 0;
                    $data[$k2]['overtime_ten_thirty_renewal_num'] = 0;
                    $data[$k2]['type'] = 1;
                    $data[$k2]['renewal_month_total'] = 0;
                    $data[$k2]['renewal_amount'] = 0;
                    $data[$k2]['renewal_user_num'] = 0;
                }
            }

            if (!empty($renewal_user_num)){
                foreach ($renewal_user_num as $k => $v){
                    $data[$k]['renewal_user_num'] = count(array_unique($v));
                }
            }

            $time = date('Y-m-d',strtotime("-1 days"));
            LeaseRenewal::where("renewal_date","=",$time)->delete();
            sort($data);
            LeaseRenewal::insert($data);



        } catch (\Exception $e) {
            \Log::error("同步租点续租统计失败: {$e->getMessage()}");
            systemLog("同步租点续租统计失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步租点续租统计失败：{$exception->getMessage()}");
        systemLog("同步租点续租统计失败: 详情请见laravel-log");
    }
}
