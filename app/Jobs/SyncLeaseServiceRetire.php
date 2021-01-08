<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Modules\Lease\Models\BlLeaseExchange;
use Carbon\Carbon;
use Modules\Lease\Models\BlLeaseRetire;
use Modules\Manage\Models\Service\LeaseServiceExchange;
use Modules\Manage\Models\Service\LeaseServiceRetire;

class SyncLeaseServiceRetire implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    //最多运行5次
    public $tries = 5;

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

            LeaseServiceRetire::where('id','>',0)->delete();
            $list =BlLeaseRetire::select("bl_lease_retires.*","bl_lease_contracts.province_id","bl_lease_contracts.agent_id",'bl_lease_contracts.service_id')
                ->leftJoin('bl_lease_services', 'bl_lease_retires.id', '=', 'bl_lease_services.serviceable_id')
                ->leftJoin('bl_lease_contracts', 'bl_lease_contracts.id', '=', 'bl_lease_services.contract_id')
                ->where('bl_lease_services.serviceable_type', '=', 'App\Models\LeaseRetireModel')
                ->where('bl_lease_retires.status','=',3)
                ->chunk(100, function ($list){
                    $list = $list->toArray();
                    if (!empty($list)) LeaseServiceRetire::insert($list);
                });
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
