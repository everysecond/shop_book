<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlServiceWithdraw;
use Modules\Manage\Models\Report\LeasePayment;
use Modules\Manage\Models\Service\LeaseServiceWithdraw;

class SyncLeaseServiceWithdraw implements ShouldQueue
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


            $maxCreatedTime = LeaseServiceWithdraw::max("created_at");
            $maxUpdatedTime = LeaseServiceWithdraw::max("updated_at");
            BlServiceWithdraw::where(function ($query) use ($maxCreatedTime, $maxUpdatedTime) {
                //查询最近一小时新增或更新的合约订单
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
                    $syncService = LeaseServiceWithdraw::where("id", $user->id)->first();
                    if (!$syncService) {
                        $syncService = new LeaseServiceWithdraw();
                    }
                    $syncService-> id= $user->id;
                    $syncService-> manager_id= $user->manager_id;
                    $syncService-> service_id= $user->service_id;
                    $syncService-> amount= $user->amount;
                    $syncService-> status= $user->status;
                    $syncService-> reply= $user->reply;
                    $syncService-> remark= $user->remark;
                    $syncService-> message= $user->message;
                    $syncService-> card_number= $user->card_number;
                    $syncService-> bank= $user->bank;
                    $syncService-> name= $user->name;
                    $syncService-> province= $user->province;
                    $syncService-> city= $user->city;
                    $syncService-> transfer_type= $user->transfer_type;
                    $syncService-> is_auto= $user->is_auto;
                    $syncService-> auto_status= $user->auto_status;
                    $syncService-> cas_vcode= $user->cas_vcode;
                    $syncService-> created_at= $user->created_at;
                    $syncService-> updated_at= $user->updated_at;
                    $syncService-> deleted_at= $user->deleted_at;
                    $syncService-> agent_id= $user->agent_id;
                    $syncService-> arrival_at= $user->arrival_at;
                    $syncService->province_id = isset(getProvinceId($user->agent_id)["id"]) ? getProvinceId($user->agent_id)["id"] : 0;
                    $syncService->save();
                }
            });
            systemLog("同步网点提现成功");
        } catch (\Exception $e) {
            \Log::error("同步网点提现失败: {$e->getMessage()}");
            systemLog("同步网点提现失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步网点提现失败：{$exception->getMessage()}");
        systemLog("同步网点提现失败: 详情请见laravel-log");
    }
}
