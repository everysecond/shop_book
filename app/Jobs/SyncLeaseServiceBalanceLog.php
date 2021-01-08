<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlServiceBalanceLog;
use Modules\Manage\Models\Service\LeaseServiceBalanceLog;


class SyncLeaseServiceBalanceLog implements ShouldQueue
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
            $maxCreatedTime = LeaseServiceBalanceLog::max("created_at");
            BlServiceBalanceLog::where(function ($query) use ($maxCreatedTime) {
                if ($maxCreatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime);
                }
            })->chunk(100, function ($logs) {
                $logArr = [];
                foreach ($logs as $k => $log) {
                    $syncLog = new LeaseServiceBalanceLog();
                    $syncLog->id = $log->id;
                    $syncLog->service_id = $log->service_id;
                    $syncLog->source = $log->source;
                    $syncLog->type = $log->type;
                    $syncLog->amount = $log->amount;
                    $syncLog->balance_before = $log->balance_before;
                    $syncLog->created_at = date("Y-m-d H:i:s", strtotime($log->created_at));
                    $syncLog->lease_service_id = $log->lease_service_id;
                    $syncLog->remark = $log->remark;
                    $syncLog->relation_type = $log->relation_type;
                    $syncLog->relation_id = $log->relation_id;
                    $logArr[] = $syncLog->toArray();
                }
                LeaseServiceBalanceLog::query()->insert($logArr);
            });
            systemLog("同步网点余额变动表任务成功");
        } catch (\Exception $e) {
            \Log::error("同步网点余额变动表任务失败: {$e->getMessage()}");
            systemLog("同步网点余额变动表任务失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步网点余额变动表任务失败：{$exception->getMessage()}");
        systemLog("同步网点余额变动表任务失败: 详情请见laravel-log");
    }
}
