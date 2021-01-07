<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlUser;
use Modules\Manage\Models\Report\LeaseUser;

class SyncLeaseUser implements ShouldQueue
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
            $maxCreatedTime = LeaseUser::max("created_at");
            $maxUpdatedTime = LeaseUser::max("updated_at");
            BlUser::where(function ($query) use ($maxCreatedTime, $maxUpdatedTime) {
                if ($maxCreatedTime && $maxUpdatedTime) {
                    $query->where("created_at", ">", date("Y-m-d H:i:s", $maxCreatedTime))
                        ->orWhere("updated_at", ">", date("Y-m-d H:i:s", $maxUpdatedTime));
                } elseif ($maxCreatedTime) {
                    $query->where("created_at", ">", date("Y-m-d H:i:s", $maxCreatedTime));
                } elseif ($maxUpdatedTime) {
                    $query->where("updated_at", ">", date("Y-m-d H:i:s", $maxUpdatedTime));
                }
            })->chunk(100, function ($users) {
                foreach ($users as $k => $user) {
                    $syncUser = LeaseUser::where("user_id", $user["id"])->first();
                    if (!$syncUser) {
                        $syncUser = new LeaseUser();
                    }
                    $syncUser->user_id = $user["id"];
                    $syncUser->mobile = $user["mobile"];
                    $syncUser->nickname = $user["nickname"];
                    $syncUser->sex = $user["sex"];
                    $syncUser->register_type = $user["register_type"];
                    $syncUser->province_id = $user["province_id"];
                    $syncUser->register_at = $user["created_at"] ? date("Y-m-d", strtotime($user["created_at"])) : "";
                    $syncUser->created_at = strtotime($user["created_at"]);
                    $syncUser->updated_at = $user["updated_at"] ? strtotime($user["updated_at"]) : 0;
                    $syncUser->register_hour = date("H", strtotime($user["created_at"])) * 1;
                    if ($bir = $user["birthday"]) {
                        $age = date("Y") - date("Y", strtotime($bir));
                        $syncUser->age = $age > 0 ? $age : 0;
                        $syncUser->birthday = $bir;
                    }
                    $syncUser->save();
                }
            });
            systemLog("同步租点用户成功");
        } catch (\Exception $e) {
            \Log::error("同步租点用户失败: {$e->getMessage()}");
            systemLog("同步租点用户失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步租点用户失败：{$exception->getMessage()}");
        systemLog("同步租点用户失败: 详情请见laravel-log");
    }
}
