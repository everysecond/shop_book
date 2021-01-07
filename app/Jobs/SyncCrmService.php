<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlService;
use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\Report\LeaseService;


class SyncCrmService implements ShouldQueue
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
            $maxCreatedTime = CrmUser::where("cus_type",2)->where('created_by',0)->max("created_at");
            $maxUpdatedTime = CrmUser::where("cus_type",2)->where('created_by',0)->max("updated_at");
            LeaseService::where(function ($query) use ($maxCreatedTime,$maxUpdatedTime) {
                if ($maxCreatedTime && $maxUpdatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime)
                        ->orWhere("updated_at", ">", $maxUpdatedTime);
                } elseif ($maxCreatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime);
                } elseif ($maxUpdatedTime) {
                    $query->where("updated_at", ">", $maxUpdatedTime);
                }

            })->chunk(100, function ($users) {

                foreach ($users as $k => $user) {
                    $syncService =  CrmUser::where("user_id", 0)->where("cus_type",2)->where("mobile",$user->mobile)->first();
                    if (!$syncService){
                        $syncService = CrmUser::where("user_id", $user->id)->where("cus_type",2)->first();
                    }

                    if (!$syncService) {
                        $syncService = new CrmUser();

                        $syncService->mobile = $user->mobile;
                        $syncService->name = $user->service_name;
                        $syncService->owner_name = $user->owner_name;
                        $syncService->agent_id = $user->agent_id;
                        $syncService->province_id = isset(getProvinceId($user->agent_id)["id"]) ? getProvinceId($user->agent_id)["id"] : 0;
                        $syncService->province_name = $user->province_name;
                        $syncService->city_id = $user->city_id;
                        $syncService->city_name = $user->city_name;
                        $syncService->county_id = $user->county_id;
                        $syncService->county_name = $user->county_name;
                        $syncService->town_id = $user->town_id;
                        $syncService->town_name = $user->town_name;
                        $syncService->address = $user->address;
                        $syncService->area = $user->province_name.$user->city_name;
                    }
                    $syncService->user_id = $user->id;

                    $syncService->cus_type = 2;
                    $syncService->cus_source = empty($user->business_id)? 1 : 2;
                    $syncService->history_deal = ($user->status == 1)? 2 : 1;
                    if (($idcard = $user->idcard) && preg_match("/[\x7f-\xff]/",$user->idcard) == 0) {
                        $syncService->sex = substr($idcard, (strlen($idcard) == 15 ? -1 : -2), 1) % 2 ? '1' : '2'; //1为男 2为女
                    } else {
                        $syncService->sex = 0;
                    }
                    $syncService->status = $user->status;
//                    if (!$user->charger_id){
//                        $syncService->charger_id = isset(getManageId($user->business_id)['manager_id'])? getManageId($user->business_id)["manager_id"] : 0;
//                        $syncService->charger_name = isset(getManageId($user->business_id)['name'])? getManageId($user->business_id)["name"] : "";
//                    }
                    $syncService->business_id = $user->business_id;
                    $syncService->business_name = isset(getManageId($user->business_id)['name'])? getManageId($user->business_id)["name"] : "";

                    $syncService->constract_begin_at = $user->constract_begin_at;
                    $syncService->constract_end_at = $user->constract_end_at;
                    $syncService->freezing_balance = $user->freezing_balance;
                    $syncService->balance = $user->balance;
                    $syncService->is_auth = empty($user->idcard)? 1 : 0;
                    $syncService->memo = $user->remark;
                    $syncService->created_at = $user->created_at;
                    $syncService->updated_at = $user->updated_at;
                    $syncService->bail = $user->bail;
                    $syncService->league = $user->league;

                    $syncService->save();
                }
            });
            systemLog("同步CRM服务点表任务成功");
        } catch (\Exception $e) {
            \Log::error("同步CRM服务点表任务失败: {$e->getMessage()}");
            systemLog("同步CRM服务点表任务失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步CRM服务点表任务失败：{$exception->getMessage()}");
        systemLog("同步CRM服务点表任务失败: 详情请见laravel-log");
    }
}
