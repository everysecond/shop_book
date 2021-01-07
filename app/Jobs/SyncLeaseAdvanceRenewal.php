<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlLeasePayment;
use Modules\Lease\Models\BlLeaseContract;
use Modules\Manage\Models\LeaseAdvanceRenewal;
use Modules\Manage\Models\LeaseRenewal;
use Carbon\Carbon;
use Modules\Manage\Models\Report\LeasePayment;

class SyncLeaseAdvanceRenewal implements ShouldQueue
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

            $maxTime = LeaseAdvanceRenewal::max('renewal_date');

            if (!$maxTime){
                $maxTime = LeaseRenewal::min('renewal_date');
            }

            LeaseRenewal::where(function ($query) use ($maxTime) {
                    $query->where("renewal_date", ">=", $maxTime);
            })->chunk(100, function ($users) {
                $users = $users->toArray();
                foreach ($users as $k => $user) {
                    $time = strtotime($user['renewal_date']);
                    $end_time = date("Y-m-d 00:00:00", strtotime("+1 days", $time));
                    $start_time = date("Y-m-d 00:00:00", $time);

                    $start_time1 = $time;
                    $end_time1 = strtotime("+6 days", $time);

                    $start_time2 = strtotime("+6 days", $time);
                    $end_time2 = strtotime("+10 days", $time);

                    $start_time3 = strtotime("+10 days", $time);
                    $end_time3 = strtotime("+30 days", $time);

//                    $start_time4 = strtotime("+30 days", $time);
//                    $end_time4 = strtotime("+5 days", $time);

                    $syncContract = new LeaseAdvanceRenewal();

                    $list = [];

                    $list = LeasePayment::where("payed_at","<=",$end_time)
                        ->where("payed_at",">",$start_time)->where("status","=",1)
                        ->whereIn("type",array('2','3'));

                    if ($user['province_id']){
                        $list = $list->where('province_id',$user['province_id']);
                    }

                    $list = $list->get()->toArray();

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


                    $a1 = $b1 = $c1 = $d1 = $a2 = $b2 = $c2 = $d2 = 0;

                    if (!empty($prev_renewal_contract_list)){
                        foreach ($prev_renewal_contract_list as $k1=>$v1){
                            if(strtotime($v1['contract_expired_at'])<=$end_time1&&strtotime($v1['contract_expired_at'])>$start_time1){
                                ++$a1;
                            }

                            if(strtotime($v1['contract_expired_at'])<=$end_time2&&strtotime($v1['contract_expired_at'])>$start_time2){
                                ++$b1;
                            }

                            if(strtotime($v1['contract_expired_at'])<=$end_time3&&strtotime($v1['contract_expired_at'])>$start_time3){
                                ++$c1;
                            }

                            if(strtotime($v1['contract_expired_at'])>$end_time3){
                                ++$d1;
                            }
                        }
                    }

                    if (!empty($prev_renewal_list)) {
                        foreach ($prev_renewal_list as $k1 => $v1) {
                            if (strtotime($v1['lease_expired_at']) <= $end_time1 && strtotime($v1['lease_expired_at']) > $start_time1) {
                                ++$a2;
                            }

                            if (strtotime($v1['lease_expired_at']) <= $end_time2 && strtotime($v1['lease_expired_at']) > $start_time2) {
                                ++$b2;
                            }

                            if (strtotime($v1['lease_expired_at']) <= $end_time3 && strtotime($v1['lease_expired_at']) > $start_time3) {
                                ++$c2;
                            }

                            if (strtotime($v1['lease_expired_at']) > $end_time3 ) {
                                ++$d2;
                            }

                        }
                    }






                    $syncContract->renewal_date = $user['renewal_date'];
                    $syncContract->advance_one_five_renewal_num = $a1+$a2;
                    $syncContract->advance_six_ten_renewal_num = $b1+$b2;
                    $syncContract->advance_ten_thirty_renewal_num = $c1+$c2;
                    $syncContract->advance_over_thirty_renewal_num = $d1+$d2;
                    $syncContract->created_at = time();
                    $syncContract->province_id = $user['province_id'];

                    $syncContract->save();

                }



            });



            systemLog("同步提前续租统计任务成功");

        } catch (\Exception $e) {
            \Log::error("同步提前续租统计失败: {$e->getMessage()}");
            systemLog("同步提前续租统计失败: 详情请见laravel-log");
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
