<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;
use Modules\Lease\Models\BlService;
use Modules\Manage\Models\Report\LeaseService;


class SyncLeaseService implements ShouldQueue
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
            LeaseService::query()->truncate();

            $maxCreatedTime = LeaseService::max("created_at");
//            $maxUpdatedTime = LeaseService::max("updated_at");
            $agents = leaseAgentCache();
            BlService::where(function ($query) use ($maxCreatedTime) {
//                if ($maxCreatedTime && $maxUpdatedTime) {
//                    $query->where("created_at", ">", date("Y-m-d H:i:s", $maxCreatedTime))
//                        ->orWhere("updated_at", ">", date("Y-m-d H:i:s", $maxUpdatedTime));
//                } elseif ($maxCreatedTime) {
//                    $query->where("created_at", ">", date("Y-m-d H:i:s", $maxCreatedTime));
//                } elseif ($maxUpdatedTime) {
//                    $query->where("updated_at", ">", date("Y-m-d H:i:s", $maxUpdatedTime));
//                }

                if ($maxCreatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime);
                }
            })->chunk(100, function ($users) use ($agents) {
                foreach ($users as $k => $user) {
                    $syncService = LeaseService::where("id", $user->id)->first();
                    if (!$syncService) {
                        $syncService = new LeaseService();
                    }
                    $syncService->id = $user->id;
                    $syncService->created_date = date("Y-m-d", strtotime($user->created_at));
                    $syncService->mobile = $user->mobile;
                    $syncService->service_name = $user->service_name;
                    $syncService->owner_name = $user->owner_name;
                    $syncService->agent_id = $user->agent_id;
                    $syncService->province_id = $user->province_id;
                    $syncService->province_name = $user->province_name;
                    $syncService->city_id = $user->city_id;
                    $syncService->city_name = $user->city_name;
                    $syncService->county_id = $user->county_id;
                    $syncService->county_name = $user->county_name;
                    $syncService->town_id = $user->town_id;
                    $syncService->town_name = $user->town_name;
                    if($agent = Arr::get($agents,$user->agent_id)){
                        $syncService->province_id = $agent['province_id'];
                        $syncService->province_name = $agent['province_name'];
                        $syncService->city_id = $agent['city_id'];
                        $syncService->city_name = $agent['city_name'];
                        $syncService->county_id = $agent['county_id'];
                        $syncService->county_name = $agent['county_name'];
                    }
                    $syncService->address = $user->address;
                    $syncService->avatar = $user->avatar;
                    $syncService->idcard = $user->idcard;
                    $syncService->idcard_front = $user->idcard_front;
                    $syncService->idcard_back = $user->idcard_back;
                    $syncService->photos = $user->photos;
                    $syncService->location = $user->location;
                    $syncService->location_hash = $user->location_hash;
                    $syncService->freezing_balance = $user->freezing_balance;
                    $syncService->balance = $user->balance;
                    $syncService->status = $user->status;
                    $syncService->bail = $user->bail;
                    $syncService->vip = $user->vip;
                    $syncService->score = $user->score;
                    $syncService->created_at = $user->created_at;
                    $syncService->experied_at = $user->experied_at;
                    $syncService->audited_at = $user->audited_at;
                    $syncService->deleted_at = $user->deleted_at;
                    $syncService->lease_limit = $user->lease_limit;
                    $syncService->lease_sku = $user->lease_sku;
                    $syncService->recycle_sku = $user->recycle_sku;
                    $syncService->back_sku = $user->back_sku;
                    $syncService->protocol = $user->protocol;
                    $syncService->business_id = $user->business_id;
                    $syncService->constract_begin_at = $user->constract_begin_at;
                    $syncService->constract_end_at = $user->constract_end_at;
                    $syncService->remark = $user->remark;
                    $syncService->market_person = $user->market_person;
                    $syncService->league = $user->league;
                    $syncService->updated_at = now();

                    if (($idcard = $user->idcard) && preg_match("/[\x7f-\xff]/", $user->idcard) == 0) {
                        $birthYear = strlen($idcard) == 15 ? ('19' . substr($idcard, 6, 2)) : substr($idcard, 6, 4);
                        $syncService->sex = substr($idcard, (strlen($idcard) == 15 ? -1 : -2), 1) % 2 ? '1' : '2'; //1为男 2为女
                        $syncService->age = date("Y") - $birthYear;
                    } else {
                        $syncService->sex = 0;
                        $syncService->age = 0;
                    }
                    $syncService->save();
                }
            });
            systemLog("同步租点服务点表任务成功");
        } catch (\Exception $e) {
            \Log::error("同步租点服务点表任务失败: {$e->getMessage()}");
            systemLog("同步租点服务点表任务失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步租点服务点表任务失败：{$exception->getMessage()}");
        systemLog("同步租点服务点表任务失败: 详情请见laravel-log");
    }
}
