<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlLeaseContract;
use Modules\Lease\Models\BlUser;
use Modules\Manage\Models\Report\LeaseProcessChannel;

class SyncLeaseProcessChannel implements ShouldQueue
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
       
                $time = date("Y-m-d 00:00:00", strtotime("-1 days"));
                $timeend = date("Y-m-d 00:00:00");
                $timeday = date("Y-m-d", strtotime("-1 days"));
                //查询出所有符合时间的数据，在和用户表 省份表进行联查
                $where[] = ['created_at', '>=', $time];
                $where[] = ['created_at', '<' , $timeend];
                //查询
                $dataa = BlUser::select("id", "province_id")
                    ->where($where)
                    ->get()
                    ->toarray();
                
                $data = [];
                $valuearr = [];
                $datachannel = ['android', 'ios'];
                if (!empty($dataa)) {
                    //按照省份进行查询 累计
                    foreach ($dataa as $value) {
                        $valuearr[] = $value['province_id'];
                        //将所有注册的用户id组成数组
                        $valueUserArr[] = $value['id'];
                    }
                    $uniquevue = array_unique($valuearr); //去除重复省份
                    $uniqueUserVue = array_unique($valueUserArr); //去除重复用户
                    foreach ($datachannel as $channel) {
                        foreach ($uniquevue as $val) {
                            //判断如果值与第一个元素相等 代表属于这个省份
                            //注册用户数
                            $data[$val][$channel]['register_num'] = BlUser::leftJoin('bl_flows', 'bl_flows.user_id', '=', 'bl_users.id')
                                ->where('bl_flows.created_at', '>=', $time)
                                ->where('bl_users.created_at', '>=', $time)
                                ->where('bl_users.created_at', '<', $timeend)
                                ->where('bl_users.province_id', $val)
                                ->where('bl_flows.system_type', $channel)
                                ->distinct()
                                ->count("bl_users.id");
                            
                            //登录用户数
                            $data[$val][$channel]['login_num'] = BlUser::leftJoin('bl_flows', 'bl_flows.user_id', '=', 'bl_users.id')
                                ->where('bl_flows.created_at', '>=', $time)
                                ->where('bl_users.created_at', '>=', $time)
                                ->where('bl_users.created_at', '<', $timeend)
                                ->where('bl_users.province_id', $val)
                                ->where('bl_flows.system_type', $channel)
                                ->where("bl_flows.page_url", 'user/login')
                                ->distinct()
                                ->count("bl_users.id");
                            //到达首页用户数
                            $data[$val][$channel]['index_num'] = $data[$val][$channel]['login_num'];
                            //发起扫码用户数
                            $data[$val][$channel]['scan_num'] = BlUser::leftJoin('bl_flows', 'bl_flows.user_id', '=',
                                'bl_users.id')
                                ->where('bl_flows.created_at', '>=', $time)
                                ->where('bl_users.created_at', '>=', $time)
                                ->where('bl_users.created_at', '<', $timeend)
                                ->where('bl_users.province_id', $val)
                                ->where('bl_flows.system_type', $channel)
                                ->where("bl_flows.page_url", 'lease/scan-code')
                                ->distinct()
                                ->count("bl_users.id");
                            //到达租赁详情页用户数
                            $data[$val][$channel]['detail_num'] = BlUser::leftJoin('bl_flows', 'bl_flows.user_id', '=',
                                'bl_users.id')
                                ->where('bl_flows.created_at', '>=', $time)
                                ->where('bl_users.created_at', '>=', $time)
                                ->where('bl_users.created_at', '<', $timeend)
                                ->where('bl_users.province_id', $val)
                                ->where('bl_flows.system_type', $channel)
                                ->where("bl_flows.page_url", 'lease/confirm')
                                ->distinct()
                                ->count("bl_users.id");
                            //选择租赁周期用户数
                            $data[$val][$channel]['period_num'] = $data[$val][$channel]['detail_num'];
                            //旧电池抵扣用户数
                            $data[$val][$channel]['deduction_num'] = $data[$val][$channel]['detail_num'];
                            //提交租赁单用户数
                            $data[$val][$channel]['submit_lease_num'] = BlUser::leftJoin('bl_flows', 'bl_flows.user_id',
                                '=', 'bl_users.id')
                                ->where('bl_flows.created_at', '>=', $time)
                                ->where('bl_users.created_at', '>=', $time)
                                ->where('bl_users.created_at', '<', $timeend)
                                ->where('bl_users.province_id', $val)
                                ->where('bl_flows.system_type', $channel)
                                ->where("bl_flows.page_url", 'lease/submit')
                                ->distinct()
                                ->count("bl_users.id");
                            //商家确认扫码用户数
                            $data[$val][$channel]['business_num'] = BlUser::leftJoin('bl_flows', 'bl_flows.user_id',
                                '=',
                                'bl_users.id')
                                ->where('bl_flows.created_at', '>=', $time)
                                ->where('bl_users.created_at', '>=', $time)
                                ->where('bl_users.created_at', '<', $timeend)
                                ->where('bl_users.province_id', $val)
                                ->where('bl_flows.system_type', $channel)
                                ->where("bl_flows.page_url", 'like', 'rents/' . '%')
                                ->distinct()
                                ->count("bl_users.id");
                            
                            //待支付页面用户数
                            $data[$val][$channel]['topay_num'] = BlLeaseContract::leftJoin('bl_users',
                                'bl_lease_contracts.user_id', '=', 'bl_users.id')
                                ->leftJoin('bl_flows', 'bl_flows.user_id', '=', 'bl_users.id')
                                ->where('bl_flows.created_at', '>=', $time)
                                ->where('bl_users.created_at', '>=', $time)
                                ->where('bl_users.created_at', '<', $timeend)
                                ->where('bl_users.province_id', $val)
                                ->where('bl_flows.system_type', $channel)
                                ->where('bl_lease_contracts.status', 1)
                                ->distinct()
                                ->count("bl_users.id");
                            //发起支付用户数
                            $data[$val][$channel]['dopay_num'] = BlLeaseContract::leftJoin('bl_users',
                                'bl_lease_contracts.user_id', '=', 'bl_users.id')
                                ->leftJoin('bl_flows', 'bl_flows.user_id', '=', 'bl_users.id')
                                ->where('bl_flows.created_at', '>=', $time)
                                ->where('bl_users.created_at', '>=', $time)
                                ->where('bl_users.created_at', '<', $timeend)
                                ->where('bl_users.province_id', $val)
                                ->where('bl_flows.system_type', $channel)
                                ->where('bl_lease_contracts.status', 2)
                                ->distinct()
                                ->count("bl_users.id");
                            //查询当前省份租赁总人数 有效租赁
                            $data[$val][$channel]['pay_num'] = BlLeaseContract::leftJoin('bl_users',
                                'bl_lease_contracts.user_id', '=', 'bl_users.id')
                                ->leftJoin('bl_flows', 'bl_flows.user_id', '=', 'bl_users.id')
                                ->where('bl_flows.created_at', '>=', $time)
                                ->where('bl_users.created_at', '>=', $time)
                                ->where('bl_users.created_at', '<', $timeend)
                                ->where('bl_users.province_id', $val)
                                ->where('bl_flows.system_type', $channel)
                                ->where('bl_lease_contracts.status', 3)
                                ->distinct()
                                ->count("bl_users.id");
                            $data[$val][$channel]['province_id'] = $val;
                            $data[$val][$channel]['type'] = 1;
                            $data[$val][$channel]['process_date'] = $timeday;
                            $data[$val][$channel]['created_at'] = time();
                            $data[$val][$channel]['updated_at'] = time();
                            $data[$val][$channel]['systemtype'] = $channel;
                        }
                    }
                   
                    //将上述各个省份的数据累加得到全部区域的数据
                    foreach ($datachannel as $channel) {
                        if (!in_array("0", $uniquevue)) {
                            $data[0][$channel]['province_id'] = 0;
                            $data[0][$channel]['register_num'] = 0;
                            $data[0][$channel]['login_num'] = 0;
                            $data[0][$channel]['index_num'] = 0;
                            $data[0][$channel]['scan_num'] = 0;
                            $data[0][$channel]['detail_num'] = 0;
                            $data[0][$channel]['deduction_num'] = 0;
                            $data[0][$channel]['period_num'] = 0;
                            $data[0][$channel]['submit_lease_num'] = 0;
                            $data[0][$channel]['business_num'] = 0;
                            $data[0][$channel]['topay_num'] = 0;
                            $data[0][$channel]['dopay_num'] = 0;
                            $data[0][$channel]['pay_num'] = 0;
                            $data[0][$channel]['systemtype'] = $channel;
                            $data[0][$channel]['process_date'] = $timeday;
                            $data[0][$channel]['type'] = 1;
                            $data[0][$channel]['created_at'] = time();
                            $data[0][$channel]['updated_at'] = time();
                        }
                        ksort($data);
                        foreach ($data as $key => $value) {
                            //全国的注册用户数
//                            $dataallregister_num = BlUser::leftJoin('bl_flows', 'bl_flows.user_id', '=', 'bl_users.id')
//                                ->where('bl_flows.created_at', '>=', $time)
//                                ->where('bl_users.created_at', '>=', $time)
//                                ->where('bl_users.created_at', '<', $timeend)
//                                ->where('bl_flows.system_type', $channel)
//                                ->distinct()
//                                ->count("bl_users.id");
//                            //全国的注册 租赁用户数
//                            $dataallregisterPay_num = BlLeaseContract::leftJoin('bl_users',
//                                'bl_lease_contracts.user_id', '=', 'bl_users.id')
//                                ->leftJoin('bl_flows', 'bl_flows.user_id', '=', 'bl_users.id')
//                                ->where('bl_flows.created_at', '>=', $time)
//                                ->where('bl_users.created_at', '>=', $time)
//                                ->where('bl_users.created_at', '<', $timeend)
//                                ->where('bl_flows.system_type', $channel)
//                                ->where('bl_lease_contracts.status', 3)
//                                ->distinct()
//                                ->count("bl_lease_contracts.user_id");
                            
//                            if ($value != 0) {
                                if ($value[$channel]['systemtype'] == $channel) {
                                    $data[0][$channel]['province_id'] = 0;
                                    $data[0][$channel]['register_num'] += $value[$channel]['register_num'];
                                    $data[0][$channel]['login_num'] += $value[$channel]['login_num'];
                                    $data[0][$channel]['index_num'] += $value[$channel]['index_num'];
                                    $data[0][$channel]['scan_num'] += $value[$channel]['scan_num'];
                                    $data[0][$channel]['detail_num'] += $value[$channel]['detail_num'];
                                    $data[0][$channel]['deduction_num'] += $value[$channel]['deduction_num'];
                                    $data[0][$channel]['period_num'] += $value[$channel]['period_num'];
                                    $data[0][$channel]['submit_lease_num'] += $value[$channel]['submit_lease_num'];
                                    $data[0][$channel]['business_num'] += $value[$channel]['business_num'];
                                    $data[0][$channel]['topay_num'] += $value[$channel]['topay_num'];
                                    $data[0][$channel]['dopay_num'] += $value[$channel]['dopay_num'];
                                    $data[0][$channel]['pay_num'] += $value[$channel]['pay_num'];
                                    $data[0][$channel]['process_date'] = $timeday;
                                    $data[0][$channel]['systemtype'] = $channel;
                                    $data[0][$channel]['type'] = 1;
                                    $data[0][$channel]['created_at'] = time();
                                    $data[0][$channel]['updated_at'] = time();
                                }
//                            }
                        }
                    }
                    ksort($data);
                    if (!empty($data)) {
                        //降维
                        $new_arr = [];
                        foreach ($data as $v) {
                            $new_arr[] = $v['android'];
                            $new_arr[] = $v['ios'];
                        }
                        LeaseProcessChannel::insert($new_arr);
                      
                    }
                   
                }
                $where=array();
                $data =array();
                $dataa =array();
                $new_arr =array();
            systemLog("租点同步统计渠道从登录到租赁的到达");
         
        } catch (\Exception $e) {
            \Log::error("租点同步统计渠道从登录到租赁的到达失败:{$e->getMessage()}");
            systemLog("租点同步统计渠道从登录到租赁的到达失败: 详情请见laravel-log或者 {$e->getMessage()}");
        }
    }
    
    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步统计从注册到租赁的到达失败：{$exception->getMessage()}");
        systemLog("同步统计从注册到租赁的到达失败: 详情请见laravel-log 或 {$exception->getMessage()}");
    }
}
