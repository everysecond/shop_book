<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseProcessPeriod;
use Modules\Manage\Models\Report\LeaseUser;

class SyncLeaseProcessPeriod implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    //最多运行5次
    public $tries = 5;
    
    /**
     * Create a new job instance.
     *
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
            
            $end_time = date("Y-m-d", strtotime("-1 days"));
            $start_time = date("Y-m-d", strtotime("-31 days"));
            $date_range = getDateRange($start_time, $end_time);
            //单次维护最近30天的数据
            $data = [];
            foreach ($date_range as $key => $value) {
                foreach ($provinces as $k3 => $v3) {
                    $sql = LeaseUser::selectRaw("id, province_id")
                        ->where("register_at", $value);
                    //按省按日期分组
                    $provinceData = $sql->get()->toArray();
                    if (!empty($provinceData)) {
                        //按省按日期分组
                        $data[$value][$k3]['register_date'] = $value;
                        $data[$value][$k3]['register_num'] = 0;
                        $data[$value][$k3]['today_num'] = 0;
                        $data[$value][$k3]['one_three_num'] = 0;
                        $data[$value][$k3]['four_seven_num'] = 0;
                        $data[$value][$k3]['eight_ten_num'] = 0;
                        $data[$value][$k3]['eleven_thirty_num'] = 0;
                        $data[$value][$k3]['thirty_no_num'] = 0;
                        $data[$value][$k3]['province_id'] = $k3;
                        $data[$value][$k3]['created_at'] = time();
                        $user[$value][$k3] = [];
                        foreach ($provinceData as $k1 => $v1) {
                            if ($v1['province_id'] == $k3) {
                                ++$data[$value][$k3]['register_num'];
                                $user[$value][$k3][] = $v1['id'];
                            }
                        }
                        
                        if (!empty($user[$value][$k3])) {
                            $time_1 = selectTimeDate(1, $value);
                            $time_1_3 = selectTimeDate(2, $value);
                            $time_4_7 = selectTimeDate(3, $value);
                            $time_8_10 = selectTimeDate(4, $value);
                            $time_11_30 = selectTimeDate(6, $value);
                            
                            $time_1_list = LeaseContract::whereIn("user_id", $user[$value][$k3])
                                ->where("status", 3)
                                ->where("created_at", "<", $time_1['end_time'])
                                ->where("created_at", ">=", $time_1['start_time'])
                                ->count("user_id");
                            
                            if ($time_1_list) {
                                $data[$value][$k3]['today_num'] = $time_1_list;
                            }
                            
                            $time_1_3_list = LeaseContract::whereIn("user_id", $user[$value][$k3])
                                ->where("status", "=", 3)
                                ->where("created_at", "<", $time_1_3['end_time'])
                                ->where("created_at", ">=", $time_1_3['start_time'])
                                ->count("user_id");
                            if ($time_1_3_list) {
                                $data[$value][$k3]['one_three_num'] = $time_1_3_list;
                            }
                            
                            $time_4_7_list = LeaseContract::whereIn("user_id", $user[$value][$k3])
                                ->where("status", "=", 3)
                                ->where("created_at", "<", $time_4_7['end_time'])
                                ->where("created_at", ">=", $time_4_7['start_time'])
                                ->count("user_id");
                            if ($time_4_7_list) {
                                $data[$value][$k3]['four_seven_num'] = $time_4_7_list;
                            }
                            
                            $time_8_10_list = LeaseContract::whereIn("user_id", $user[$value][$k3])
                                ->where("status", "=", 3)
                                ->where("created_at", "<", $time_8_10['end_time'])
                                ->where("created_at", ">=", $time_8_10['start_time'])
                                ->count("user_id");
                            if ($time_8_10_list) {
                                $data[$value][$k3]['eight_ten_num'] = $time_8_10_list;
                            }
                            
                            $time_11_30_list = LeaseContract::whereIn("user_id", $user[$value][$k3])
                                ->where("status", "=", 3)
                                ->where("created_at", "<", $time_11_30['end_time'])
                                ->where("created_at", ">=", $time_11_30['start_time'])
                                ->count("user_id");
                            if ($time_11_30_list) {
                                $data[$value][$k3]['eleven_thirty_num'] = $time_11_30_list;
                            }
                            
                            $data[$value][$k3]['thirty_no_num'] = count($user[$value][$k3]) - $time_1_list
                                - $time_1_3_list - $time_4_7_list - $time_8_10_list - $time_11_30_list;
                            
                        }
                    }
                }
            }
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    foreach ($value as $k1 => $v1) {
                        if ($v1 != 0) {
                            $value[0]['register_date'] = $v1['register_date'];
                            $value[0]['province_id'] = 0;
                            $value[0]['register_num'] += $v1['register_num'];
                            $value[0]['today_num'] += $v1['today_num'];
                            $value[0]['one_three_num'] += $v1['one_three_num'];
                            $value[0]['four_seven_num'] += $v1['four_seven_num'];
                            $value[0]['eight_ten_num'] += $v1['eight_ten_num'];
                            $value[0]['eleven_thirty_num'] += $v1['eleven_thirty_num'];
                            $value[0]['thirty_no_num'] += $v1['thirty_no_num'];
                        }
                    }
                    //先删除再覆盖
                    LeaseProcessPeriod::where("register_date", "=", $key)->delete();
                    LeaseProcessPeriod::insert($value);
                }
                systemLog("注册-租赁发起周期定时任务运行成功");
            } else {
                systemLog("当天没有注册数据（注册-租赁发起周期定时任务）");
            }
        } catch (\Exception $e) {
            \Log::error("注册-租赁发起周期定时任务运行失败:{$e->getMessage()}");
            systemLog("注册-租赁发起周期定时任务运行失败: 详情请见laravel-log");
        }
    }
    
    /**
     * @param \Exception $exception
     */
    public
    function failed(
        \Exception $exception
    ) {
        \Log::error("注册-租赁发起周期定时任务运行失败: {$exception->getMessage()}");
        systemLog("注册-租赁发起周期定时任务运行失败: 详情请见laravel-log");
    }
}
