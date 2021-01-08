<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlFlow;
use Modules\Lease\Models\BlLeaseContract;
use Modules\Lease\Models\BlUser;
use Modules\Manage\Models\Report\LeaseProcessRegister;

class SyncLeaseProcessRegister implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    //续租是改状态加日期，续约是新增合约
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
            //查询条件 时间：本时间所在整点小时
            $time = date("Y-m-d H:i:s", strtotime("-1 hour"));
            $timeend = date("Y-m-d H:i:s");
            $where[] = ['created_at', '>=', $time];
            $where[] = ['created_at', '<', $timeend];
            
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $agentsMap[0] = '全部区域';
            foreach ($agentsMap as $val => $value) {
                $data[$val]['register_num'] = 0;
                $data[$val]['login_num'] = 0;
                $data[$val]['index_num'] = 0;
                $data[$val]['scan_num'] = 0;
                $data[$val]['detail_num'] = 0;
                $data[$val]['period_num'] = 0;
                $data[$val]['deduction_num'] = 0;
                $data[$val]['submit_lease_num'] = 0;
                $data[$val]['mylease_num'] = 0;
                $data[$val]['business_num'] = 0;
                $data[$val]['topay_num'] = 0;
                $data[$val]['dopay_num'] = 0;
                $data[$val]['pay_num'] = 0;
                $data[$val]['province_id'] = $val;
                $data[$val]['process_date'] = date("Y-m-d", strtotime("-1 hour"));
                $data[$val]['insert_hour'] = date("H", strtotime("-1 hour"));
                $data[$val]['type'] = 1;
                $data[$val]['created_at'] = strtotime("-1 hour");
                $data[$val]['updated_at'] = strtotime("-1 hour");
                //判断如果值与第一个元素相等 代表属于这个省份
                //查询当前省份当前小时的注册总人数
                $data[$val]['register_num'] = BlUser::where($where)->where('province_id', $val)->count('id');
                //到达登录用户数
                $data[$val]['login_num'] = BlFlow::leftJoin('bl_users', 'bl_users.id', '=', 'bl_flows.user_id')
                    ->where('bl_flows.created_at', '>=', $time)
                    ->where('bl_flows.created_at', '<', $timeend)
                    ->where('bl_users.created_at', '>=', $time)
                    ->where('bl_users.created_at', '<', $timeend)
                    ->where('bl_users.province_id', $val)
                    ->where("bl_flows.page_url", 'user/login')
                    ->distinct()->count('bl_users.id');
                //到达首页用户数
                $data[$val]['index_num'] = $data[$val]['login_num'];
                //发起扫码用户数
                $data[$val]['scan_num'] = BlFlow::leftJoin('bl_users', 'bl_users.id', '=',
                    'bl_flows.user_id')->where('bl_flows.created_at', '>=', $time)->where('bl_flows.created_at', '<',
                    $timeend)->where('bl_users.created_at', '>=', $time)
                    ->where('bl_users.created_at', '<', $timeend)->where('bl_users.province_id',
                        $val)->where("bl_flows.page_url", 'lease/scan-code')->distinct()->count('bl_users.id');
                //到达租赁详情页用户数
                $data[$val]['detail_num'] = BlFlow::leftJoin('bl_users', 'bl_users.id', '=',
                    'bl_flows.user_id')->where('bl_flows.created_at', '>=', $time)->where('bl_flows.created_at', '<',
                    $timeend)->where('bl_users.created_at', '>=', $time)
                    ->where('bl_users.created_at', '<', $timeend)->where('bl_users.province_id',
                        $val)->where("bl_flows.page_url", 'lease/confirm')->distinct()->count('bl_users.id');
                //选择租赁周期用户数
                $data[$val]['period_num'] = $data[$val]['detail_num'];
                //旧电池抵扣用户数
                $data[$val]['deduction_num'] = $data[$val]['detail_num'];
                //提交租赁单用户数
                $data[$val]['submit_lease_num'] = BlFlow::leftJoin('bl_users', 'bl_users.id', '=',
                    'bl_flows.user_id')->where('bl_flows.created_at', '>=', $time)->where('bl_flows.created_at', '<',
                    $timeend)->where('bl_users.created_at', '>=', $time)
                    ->where('bl_users.created_at', '<', $timeend)->where('bl_users.province_id',
                        $val)->where("bl_flows.page_url", 'lease/submit')->distinct()->count('bl_users.id');
                //我的租赁单用户数
                $data[$val]['mylease_num'] = BlFlow::leftJoin('bl_users', 'bl_users.id', '=',
                    'bl_flows.user_id')->where('bl_flows.created_at', '>=', $time)->where('bl_flows.created_at', '<',
                    $timeend)->where('bl_users.created_at', '>=', $time)
                    ->where('bl_users.created_at', '<', $timeend)->where('bl_users.province_id',
                        $val)->where("bl_flows.page_url", 'my-lease')->distinct()->count('bl_users.id');
                //商家确认扫码用户数
                $data[$val]['business_num'] = BlFlow::leftJoin('bl_users', 'bl_users.id', '=',
                    'bl_flows.user_id')->where('bl_flows.created_at', '>=', $time)->where('bl_flows.created_at', '<',
                    $timeend)->where('bl_users.created_at', '>=', $time)->where('bl_users.created_at', '<', $timeend)->where('bl_users.province_id',$val)->where("bl_flows.page_url", 'like', 'rents/' . '%')->distinct()->count('bl_users.id');
                //待支付页面用户数
                $data[$val]['topay_num'] = BlLeaseContract::where('status', 1)->where('province_id', $val)->where($where)->count('user_id');
                //发起支付用户数
                $data[$val]['dopay_num'] = BlLeaseContract::where('status', 2)->where('province_id', $val)->where($where)->count('user_id');
                //查询当前省份在当前小时的注册的租赁总人数 有效租赁
                $data[$val]['pay_num']   = BlLeaseContract::where('status', 3)->where('province_id', $val)->where($where)->count('user_id');
            }
            
            //查询一条全国的注册数据和全国的租赁单
            $UserAllRegister = BlUser::where($where)->count('id');
            $UserAllRegisterPay = BlLeaseContract::where('status', 3)->where($where)->count('user_id');
            sort($data);
            //将上述各个省份的数据累加得到全部区域的数据
            foreach ($data as $key => $value) {
                $data[0]['province_id'] = 0;
                $data[0]['register_num'] = $UserAllRegister;
                $data[0]['login_num'] += $value['login_num'];
                $data[0]['index_num'] += $value['index_num'];
                $data[0]['scan_num'] += $value['scan_num'];
                $data[0]['detail_num'] += $value['detail_num'];
                $data[0]['deduction_num'] += $value['deduction_num'];
                $data[0]['period_num'] += $value['period_num'];
                $data[0]['submit_lease_num'] += $value['submit_lease_num'];
                $data[0]['mylease_num'] += $value['mylease_num'];
                $data[0]['business_num'] += $value['business_num'];
                $data[0]['topay_num'] += $value['topay_num'];
                $data[0]['dopay_num'] += $value['dopay_num'];
                $data[0]['pay_num'] = $UserAllRegisterPay;
            }
            if (!empty($data)) {
                LeaseProcessRegister::insert($data);
            }
            systemLog("每个小时对租点同步统计从注册到租赁的到达");
            
        } catch (\Exception $e) {
            \Log::error("同步统计从注册到租赁的到达失败: {$e->getMessage()}");
            systemLog("同步统计从注册到租赁的到达失败: 详情请见laravel-log");
        }
    }
    
    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步统计从注册到租赁的到达失败：{$exception->getMessage()}");
        systemLog("同步统计从注册到租赁的到达失败: 详情请见laravel-log");
    }
}
