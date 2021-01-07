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
use Modules\Manage\Models\LeaseRenewalReport;

class SyncLeaseRenewalReport implements ShouldQueue
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

            $end_time =  date("Y-m-d");
            $end_time_r =  date("Y-m-d", strtotime("-1 days"));
            $start_time = date("Y-m-d", strtotime("-31 days"));
            $date_range =  getDateRange($start_time,$end_time_r);


            //续约统计
            $list = BlLeaseContract::selectRaw("id,user_id,province_id,DATE_FORMAT(contract_expired_at, '%Y-%m-%d') as contract_expired_at,
                DATE_FORMAT(lease_expired_at, '%Y-%m-%d') as lease_expired_at")
                ->where(function ($query) use($start_time,$end_time){
                    $query->Where(function ($query) use($start_time,$end_time){
                        $query->where("contract_expired_at","<",$end_time)
                            ->where("contract_expired_at",">=",$start_time);
                    })->orWhere(function ($query) use($start_time,$end_time){
                        $query->where("lease_expired_at","<",$end_time)
                            ->where("lease_expired_at",">=",$start_time);
                    });
                })
                ->whereIn("status",[3,7,8])
                ->get()->toArray();


            $data = [];
            foreach ($date_range as $key=>$value){
                foreach ($provinces as $k3 =>$v3){
                    $data[$value][$k3]['expire_rent_num'] = 0;
                    $data[$value][$k3]['expire_renewal_num'] = 0;
                    $data[$value][$k3]['overtime_one_three_renewal_future_num'] = 0;
                    $data[$value][$k3]['overtime_four_seven_renewal_future_num'] = 0;
                    $data[$value][$k3]['overtime_eight_ten_renewal_future_num'] = 0;
                    $data[$value][$k3]['overtime_ten_thirty_renewal_future_num'] = 0;
                    $data[$value][$k3]['overtime_thirty_no_renewal_future_num'] = 0;
                    $data[$value][$k3]['province_id'] = $k3;
                    $data[$value][$k3]['renewal_date'] = $value;
                    $data[$value][$k3]['created_at'] = time();
                    $user[$value][$k3] = [];
                    if (!empty($list)){
                        foreach ($list as $k1 =>$v1){
                            if (($v1['contract_expired_at'] == $value || $v1['lease_expired_at'] == $value ) && $v1['province_id'] == $k3){
                                ++$data[$value][$k3]['expire_rent_num'];
                                $user[$value][$k3][] = $v1['user_id'];
                            }

                        }

                        if (!empty($user[$value][$k3])){
                            $time_1 = selectTimeDate(1,$value);
                            $time_1_3 = selectTimeDate(2,$value);
                            $time_4_7 = selectTimeDate(3,$value);
                            $time_8_10 = selectTimeDate(4,$value);
                            $time_11_30 = selectTimeDate(6,$value);
                            $time_1_30 = selectTimeDate(8,$value);

                            $time_1_list = BlLeasePayment::distinct()
                                ->whereIn("user_id",$user[$value][$k3])
                                ->where("status","=",1)
                                ->where(function ($query){
                                    $query->where('type', '=', 3)->orWhere('type', "=", 2);
                                })
                                ->where("payed_at","<",$time_1['end_time'])
                                ->where("payed_at",">=",$time_1['start_time'])
                                ->count("user_id");

                            if ($time_1_list) $data[$value][$k3]['expire_renewal_num'] = $time_1_list;

                            $time_1_3_list = BlLeasePayment::distinct()
                                ->whereIn("user_id",$user[$value][$k3])
                                ->where("status","=",1)
                                ->where(function ($query){
                                    $query->where('type', '=', 3)->orWhere('type', "=", 2);
                                })
                                ->where("payed_at","<",$time_1_3['end_time'])
                                ->where("payed_at",">=",$time_1_3['start_time'])
                                ->count("user_id");
                            if ($time_1_3_list) $data[$value][$k3]['overtime_one_three_renewal_future_num'] = $time_1_3_list;

                            $time_4_7_list = BlLeasePayment::distinct()
                                ->whereIn("user_id",$user[$value][$k3])
                                ->where("status","=",1)
                                ->where(function ($query){
                                    $query->where('type', '=', 3)->orWhere('type', "=", 2);
                                })
                                ->where("payed_at","<",$time_4_7['end_time'])
                                ->where("payed_at",">=",$time_4_7['start_time'])
                                ->count("user_id");
                            if ($time_4_7_list) $data[$value][$k3]['overtime_four_seven_renewal_future_num'] = $time_4_7_list;

                            $time_8_10_list = BlLeasePayment::distinct()
                                ->whereIn("user_id",$user[$value][$k3])
                                ->where("status","=",1)
                                ->where(function ($query){
                                    $query->where('type', '=', 3)->orWhere('type', "=", 2);
                                })
                                ->where("payed_at","<",$time_8_10['end_time'])
                                ->where("payed_at",">=",$time_8_10['start_time'])
                                ->count("user_id");
                            if ($time_8_10_list) $data[$value][$k3]['overtime_eight_ten_renewal_future_num'] = $time_8_10_list;

                            $time_11_30_list =BlLeasePayment::distinct()
                                ->whereIn("user_id",$user[$value][$k3])
                                ->where("status","=",1)
                                ->where(function ($query){
                                    $query->where('type', '=', 3)->orWhere('type', "=", 2);
                                })
                                ->where("payed_at","<",$time_11_30['end_time'])
                                ->where("payed_at",">=",$time_11_30['start_time'])
                                ->count("user_id");
                            if ($time_11_30_list) $data[$value][$k3]['overtime_ten_thirty_renewal_future_num'] = $time_11_30_list;

                            $time_1_30_list = BlLeasePayment::distinct()
                                ->whereIn("user_id",$user[$value][$k3])
                                ->where("status","=",1)
                                ->where(function ($query){
                                    $query->where('type', '=', 3)->orWhere('type', "=", 2);
                                })
                                ->where("payed_at","<",$time_1_30['end_time'])
                                ->where("payed_at",">=",$time_1_30['start_time'])
                                ->count("user_id");

                            if ($time_1_30_list){
                                $data[$value][$k3]['overtime_thirty_no_renewal_future_num'] = count($user[$value][$k3]) - $time_1_30_list;
                            }else{
                                $data[$value][$k3]['overtime_thirty_no_renewal_future_num'] = count($user[$value][$k3]);
                            }

                        }
                    }

                }

            }

            foreach ($data as $key =>$value){
                foreach ($value as $k1 => $v1){
                    $value[0]['expire_rent_num'] += $v1['expire_rent_num'];
                    $value[0]['expire_renewal_num'] += $v1['expire_renewal_num'];
                    $value[0]['overtime_one_three_renewal_future_num'] += $v1['overtime_one_three_renewal_future_num'];
                    $value[0]['overtime_four_seven_renewal_future_num'] += $v1['overtime_four_seven_renewal_future_num'];
                    $value[0]['overtime_eight_ten_renewal_future_num'] += $v1['overtime_eight_ten_renewal_future_num'];
                    $value[0]['overtime_ten_thirty_renewal_future_num'] += $v1['overtime_ten_thirty_renewal_future_num'];
                    $value[0]['overtime_thirty_no_renewal_future_num'] += $v1['overtime_thirty_no_renewal_future_num'];
                }
                //先删除再覆盖
                LeaseRenewalReport::where("renewal_date","=",$key)->delete();
               LeaseRenewalReport::insert($value);

            }




        } catch (\Exception $e) {
            \Log::error("同步租点续租报表统计失败: {$e->getMessage()}");
            systemLog("同步租点续租报表统计失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步租点续租报表统计失败：{$exception->getMessage()}");
        systemLog("同步租点续租报表统计失败: 详情请见laravel-log");
    }
}
