<?php

namespace App\Jobs;

use Doctrine\DBAL\Schema\AbstractAsset;
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

class SyncCrmServiceNoRentUser implements ShouldQueue
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

            //网点未成单流入
            $rule = CrmRuleSetting::where("type", 2)->first()->toArray();
            $rule = json_decode($rule['json'], true);

            if ($rule['inflow_rules'] == 1 && $rule['branch_register'] == 1 && $rule['international_waters_1'] > 0) {
                $time = time();
                CrmUser::query()->where("cus_type", CrmUser::CUS_TYPE_TWO)
                    ->whereIn('status', [0,2])
                    ->where('charger_id', 0)
                    ->where('inflow_time','<',$time - 86400 * $rule['crm_input'])
                    ->where('sea_type','=',0)
                    ->chunk(100, function ($list) use ($rule, $time) {
                        $list = $list->toArray();

                        foreach ($list as $key => $value) {

                            $syncCrmUser = CrmUser::where("user_id", $value["user_id"])->where('cus_type', '=', CrmUser::CUS_TYPE_TWO)->first();
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
