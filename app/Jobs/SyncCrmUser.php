<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlUser;
use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\Report\LeaseUser;

class SyncCrmUser implements ShouldQueue
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
            $type = CrmUser::CUS_TYPE_ONE;
            $source = CrmUser::CUS_SOURCE_TWO;
            $level = CrmUser::CUS_LEVEL_TWO;
            $leaseAgentCache = leaseAgentCache();
            $maxCreatedTime = CrmUser::type($type)->where('created_by', 0)->max("created_at");
            $maxUpdatedTime = CrmUser::type($type)->where('created_by', 0)->max("updated_at");
            BlUser::query()->where(function ($query) use ($maxCreatedTime, $maxUpdatedTime) {
                if ($maxCreatedTime && $maxUpdatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime)
                        ->orWhere("updated_at", ">", $maxUpdatedTime);
                } elseif ($maxCreatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime);
                } elseif ($maxUpdatedTime) {
                    $query->where("updated_at", ">", $maxUpdatedTime);
                }
            })->with('hasValidContract')->chunk(100,
                function ($users) use ($type, $source, $level, $leaseAgentCache) {
                    foreach ($users as $k => $user) {
                        $syncUser = CrmUser::query()->where("user_id", 0)->type($type)->where("mobile", $user->mobile)->first();
                        if (!$syncUser) {
                            $syncUser = CrmUser::query()->where("user_id", $user->id)->type($type)->first();
                        }
                        if (!$syncUser) {
                            $syncUser = new CrmUser();
                            $syncUser->mobile = $user->mobile;
                            $syncUser->name = $user->nickname;
                            $syncUser->owner_name = $user->nickname;
                            $syncUser->short_name = $user->nickname;
                            $syncUser->cus_type = $type;

                            if ($user->agent_id && isset($leaseAgentCache[$user->agent_id])) {
                                $syncUser->agent_id = $user->agent_id;
                                $agentDetail = $leaseAgentCache[$user->agent_id];
                                $syncUser->province_id = $agentDetail['province_id'];
                                $syncUser->province_name = $agentDetail['province_name'];
                                $syncUser->city_id = $agentDetail['city_id'];
                                $syncUser->city_name = $agentDetail['city_name'];
                                $syncUser->county_id = $agentDetail['county_id'];
                                $syncUser->county_name = $agentDetail['county_name'];
                                $syncUser->area = $agentDetail['province_name'] . $agentDetail['city_name'];
                            } else {
                                $syncUser->agent_id = $user->agent_id;
                                $syncUser->province_id = $user->province_id;
                            }
                        }

                        $syncUser->user_id = $user->id;
//                        $syncUser->cus_level = $level;
                        $syncUser->cus_source = $source;
                        $syncUser->history_deal = $user->hasValidContract ? CrmUser::CUS_DEAL_TWO : CrmUser::CUS_DEAL_ONE;
                        $syncUser->sex = $user->sex;
                        $syncUser->status = $user->status;

                        $syncUser->deposit = $user->deposit;
                        $syncUser->freezing_balance = $user->freezing_balance;
                        $syncUser->balance = $user->balance;
                        $syncUser->prepayment = $user->prepayment;
                        $syncUser->is_auth = $user->is_auth;
                        $syncUser->created_at = $user->created_at;
                        $syncUser->updated_at = $user->updated_at;
                        $syncUser->deleted_at = $user->deleted_at;
                        $syncUser->birthday = $user->birthday;
                        $syncUser->save();
                    }
                });
            systemLog("同步Crm 租点C端用户任务成功");
        } catch (\Exception $e) {
            \Log::error("同步Crm 租点C端用户任务失败: {$e->getMessage()}");
            systemLog("同步Crm 租点C端用户任务失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步Crm 租点C端用户任务失败：{$exception->getMessage()}");
        systemLog("同步Crm 租点C端用户任务失败: 详情请见laravel-log");
    }
}
