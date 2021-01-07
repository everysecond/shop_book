<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlLeaseContract;
use Modules\Manage\Models\Crm\CrmContract;
use Modules\Manage\Models\Crm\CrmRuleSetting;
use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\Report\LeaseContract;

class SyncCrmLeaseInflowUser implements ShouldQueue
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

            $rule = CrmRuleSetting::where("type", 1)->first()->toArray();

            $rule = json_decode($rule['json'], true);

            //未租赁流入
            if ($rule['inflow_rules'] == 1 && $rule['unleased'] == 1 && $rule['international_waters_1'] > 0) {
                $time = time();
                $start_time = date('Y-m-d H:i:s', $time);
                $end_time = date('Y-m-d H:i:s', 86400 * $rule['lease_expires'] + $time);
                CrmUser::query()->where("cus_type", CrmUser::CUS_TYPE_ONE)
                    ->where('sea_type','=',0)
//                    ->where('deposit','=',0)
                    ->where('charger_id','=',0)
                    ->chunk(100, function ($list) use ($rule, $time) {
                        $list = $list->toArray();
                        foreach ($list as $key => $value) {
                            $constract = LeaseContract::whereIn('status',[2,3,7,8])->where('user_id',$value['user_id'])->orderBy('id','desc')->first();
                            if (strtotime($constract->contract_expired_at)>$time || strtotime($constract->lease_expired_at)>$time ){
                                continue;
                            }

                            $syncCrmUser = CrmUser::where("user_id", $value["user_id"])->where('cus_type', '=', 1)->first();
                            if (!$syncCrmUser) {
                                continue;
                            }

                            $syncCrmUser->pre_charger_name = $value["charger_name"];
                            $syncCrmUser->pre_charger_id = $value["charger_id"];
                            $syncCrmUser->sea_type = $rule['international_waters_1'];
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
