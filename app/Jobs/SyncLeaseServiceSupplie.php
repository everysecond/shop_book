<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlService;
use Modules\Lease\Models\BlServiceRetrieve;
use Modules\Lease\Models\BlServiceSupplie;
use Modules\Lease\Models\BlServiceSupplies;
use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Report\LeaseServiceRetrieve;
use Modules\Manage\Models\Report\LeaseServiceStockCancel;
use Modules\Manage\Models\Report\LeaseServiceSupplie;


class SyncLeaseServiceSupplie implements ShouldQueue
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
            $maxCreatedTime = LeaseServiceSupplie::max("date");
            if (!$maxCreatedTime) {$maxCreatedTime = '2018-9-30';}
            //服务器是今天运行昨天的数据，所以要减去一天
            $maxCreatedTimeend = date("Y-m-d");
            //补货
            BlServiceSupplie::where(function ($query) use ($maxCreatedTime,$maxCreatedTimeend) {
                $query->leftjoin('bl_services', 'bl_services.id', '=', 'bl_service_supplies.service_id')
                    ->selectRaw("bl_service_supplies.created_at,bl_service_supplies.service_id, bl_service_supplies.num")
                    ->where("bl_service_supplies.created_at", ">", $maxCreatedTime)
                    ->where("bl_service_supplies.created_at", "<", $maxCreatedTimeend)
                    ->orderby("bl_service_supplies.created_at",'desc');
            })->chunk(100, function ($logs) {
                $logArr = [];
                foreach ($logs as $k => $log) {
                    $syncLog = new LeaseServiceSupplie();
                    $syncLog->service_id = $log->service_id;
                    $syncLog->num = $log->num;
                    $syncLog->date = $log->created_at;
                    $logArr[] = $syncLog->toArray();
                }

                LeaseServiceSupplie::query()->insert($logArr);
            });
    
            //退货&回收
            BlServiceRetrieve::selectRaw("created_at,service_id,num,status")
                    ->where("created_at", ">", $maxCreatedTime)
                    ->where("created_at", "<", $maxCreatedTimeend)
                    ->whereIn("status", [2,3])
                    ->orderby("created_at",'desc')
                    ->chunk(100, function ($logs) {
                        $logArr = [];
                        foreach ($logs as $k => $log) {
                            $syncLog = new LeaseServiceRetrieve();
                            $syncLog->service_id = $log->service_id;
                            $syncLog->status = $log->status;
                            $syncLog->num = $log->num;
                            $syncLog->date = $log->created_at;
                            $logArr[] = $syncLog->toArray();
                }
    
                 LeaseServiceRetrieve::query()->insert($logArr);
            });
            
            
            systemLog("同步网点每日补货/退货/回收统计的任务成功");
        } catch (\Exception $e) {
            \Log::error("同步网点每日补货/退货/回收统计的任务失败: {$e->getMessage()}");
            systemLog("同步网点每日补货/退货/回收统计的任务失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步网点每日补货/退货/回收统计的任务失败：{$exception->getMessage()}");
        systemLog("同步网点每日补货/退货/回收统计的任务失败: 详情请见laravel-log");
    }
}
