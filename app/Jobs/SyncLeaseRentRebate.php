<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Modules\Lease\Models\BlLeaseRetire;
use Modules\Manage\Models\LeaseRentRebate;
use Carbon\Carbon;

class SyncLeaseRentRebate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //最多运行5次
    public $tries = 5;
    public $rent_release_num = [];
    public $advance_rent_release = [];
    public $expire_rent_release_num = [];
    public $overtime_ten_rent_release_num = [];
    public $overtime_ten_thirty_rent_release_num = [];
    public $rent_release_amount = [];
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
                $this->rent_release_num[$k] = 0;
                $this->advance_rent_release[$k] = 0;
                $this->expire_rent_release_num[$k] = 0;
                $this->overtime_ten_rent_release_num[$k] = 0;
                $this->overtime_ten_thirty_rent_release_num[$k] = 0;
                $this->rent_release_amount[$k] = 0;

            }
            //昨日退租
            $list = BlLeaseRetire::select("confirmed_at","contract_expired_at","province_id",'amount')
                ->leftJoin('bl_lease_services', 'bl_lease_retires.id', '=', 'bl_lease_services.serviceable_id')
                ->leftJoin('bl_lease_contracts', 'bl_lease_contracts.id', '=', 'bl_lease_services.contract_id')
                ->where('bl_lease_services.serviceable_type', '=', 'App\Models\LeaseRetireModel')
                ->where('bl_lease_retires.confirmed_at','>',Carbon::yesterday())
                ->where('bl_lease_retires.status','=',3)
                ->where('bl_lease_retires.confirmed_at','<',Carbon::today())
                ->chunk(100, function ($list) use($provinces){

                    $time_1_10 = selectTimeStrtotime(5);
                    $time_11_30 = selectTimeStrtotime(6);
                    $time_1 = selectTimeStrtotime(1);

                    if (!empty($list)){
                        foreach ($provinces as $k=>$v){
                            foreach ($list as $key=>$value){
                                if ($k==$value['province_id']){
                                    ++$this->rent_release_num[$k];
                                    ++$this->rent_release_num[0];
                                }
                                if ($value['contract_expired_at'] > Carbon::today()&& $k==$value['province_id']){
                                    ++$this->advance_rent_release[$k];
                                    ++$this->advance_rent_release[0];
                                }
                                if(strtotime($value['contract_expired_at'])<=$time_1['end_time']&&strtotime($value['contract_expired_at'])>$time_1['start_time']&& $k==$value['province_id']){
                                    ++$this->expire_rent_release_num[$k];
                                    ++$this->expire_rent_release_num[0];
                                }
                                if(strtotime($value['contract_expired_at'])<=$time_1_10['end_time']&&strtotime($value['contract_expired_at'])>$time_1_10['start_time'] && $k==$value['province_id']){
                                    ++$this->overtime_ten_rent_release_num[$k];
                                    ++$this->overtime_ten_rent_release_num[0];
                                }
                                if(strtotime($value['contract_expired_at'])<=$time_11_30['end_time']&&strtotime($value['contract_expired_at'])>$time_11_30['start_time'] && $k==$value['province_id']){
                                    ++$this->overtime_ten_thirty_rent_release_num[$k];
                                    ++$this->overtime_ten_thirty_rent_release_num[0];
                                }

                                if ($k == $value['province_id']) {
                                    $this->rent_release_amount[$k] += $value['amount'];
                                    $this->rent_release_amount[0] += $value['amount'];
                                }
                            }
                        }

                    }

                });

            $data = [];
            foreach ($provinces as $k2=>$v3){
                $data[$k2]['rent_release_date'] = Carbon::yesterday();
                $data[$k2]['rent_release_amount'] = $this->rent_release_amount[$k2];
                $data[$k2]['created_at'] = time();
                $data[$k2]['province_id'] = $k2;
                $data[$k2]['rent_release_num'] = $this->rent_release_num[$k2];
                $data[$k2]['advance_rent_release'] = $this->advance_rent_release[$k2];
                $data[$k2]['expire_rent_release_num'] = $this->expire_rent_release_num[$k2];
                $data[$k2]['overtime_ten_rent_release_num'] = $this->overtime_ten_rent_release_num[$k2];
                $data[$k2]['overtime_ten_thirty_rent_release_num'] = $this->overtime_ten_thirty_rent_release_num[$k2];
                $data[$k2]['type'] = 1;
            }
            sort($data);
           LeaseRentRebate::insert($data);

        } catch (\Exception $e) {
            \Log::error("同步租点退租统计失败: {$e->getMessage()}");
            systemLog("同步租点续租统计失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步租点退租统计失败：{$exception->getMessage()}");
        systemLog("同步租点退租统计失败: 详情请见laravel-log");
    }
}
