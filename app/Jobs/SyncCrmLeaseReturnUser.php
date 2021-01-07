<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlLeaseContract;
use Modules\Manage\Models\Crm\CrmContract;
use Modules\Manage\Models\Crm\CrmPlanRecord;
use Modules\Manage\Models\Crm\CrmRuleSetting;
use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\Report\LeaseContract;

class SyncCrmLeaseReturnUser implements ShouldQueue
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


//            $constract = LeaseContract::whereIn('status',[2,3])->where('user_id',37024)->pluck('deposit')->toArray();
//        $constract = $constract->toArray();
//            if ($constract != 0){
//
//                dd($constract);
//
//            }

            //N天未跟进返还到期
            $rule = CrmRuleSetting::where("type", 1)->first()->toArray();
            $rule = json_decode($rule['json'], true);

            if ($rule['return_rules'] == 1 && $rule['no_track']> 0 &&  $rule['international_waters_2'] > 0) {
                $time = time();

                CrmUser::query()->where("cus_type", CrmUser::CUS_TYPE_ONE)
                    ->where('allotted_time','<=',$time-86400 * $rule['no_track'])

                    ->where('sea_type','=',0)
                    ->chunk(100, function ($list) use ($rule, $time) {
                        $list = $list->toArray();

                        foreach ($list as $key => $value) {

                            $constract = LeaseContract::whereIn('status',[2,3,7,8])->where('user_id',$value['user_id'])->orderBy('id','desc')->first();
                            if (strtotime($constract->contract_expired_at)>$time || strtotime($constract->lease_expired_at)>$time ){
                                continue;
                            }

                            $track = CrmPlanRecord::where('follow_at','>=',$time-86400 * $rule['no_track'])
                               ->where('follow_at','<=',$time)->where("cus_id", $value["id"])->first();
                            if ($track) {
                                continue;
                            }


                            $syncCrmUser = CrmUser::where("user_id", $value["user_id"])->where('cus_type', '=', 1)->first();
                            if (!$syncCrmUser) {
                                continue;
                            }

                            $syncCrmUser->pre_charger_name = $value["charger_name"];
                            $syncCrmUser->pre_charger_id = $value["charger_id"];
                            $syncCrmUser->sea_type = $rule['international_waters_2'];
                            $syncCrmUser->charger_name = '';
                            $syncCrmUser->charger_id = 0;
                            $syncCrmUser->inflow_time = $time;
                            $syncCrmUser->save();
                        }


                    });


            }

            systemLog("同步租点合约订单成功");
        } catch (\Exception $e) {
            \Log::error("同步租点合约订单失败: {$e->getMessage()}");
            systemLog("同步租点合约订单失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步租点合约订单失败：{$exception->getMessage()}");
        systemLog("同步租点合约订单失败: 详情请见laravel-log");
    }
}
