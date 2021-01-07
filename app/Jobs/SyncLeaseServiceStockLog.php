<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Manage\Services\SyncJobService;

class SyncLeaseServiceStockLog implements ShouldQueue
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
            $service = new SyncJobService();
            $service->syncLeaseServiceStockLog();
            systemLog("同步网点库存日志成功");
        } catch (\Exception $e) {
            \Log::error("同步网点库存日志失败: {$e->getMessage()}");
            systemLog("同步网点库存日志失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步网点库存日志失败：{$exception->getMessage()}");
        systemLog("同步网点库存日志失败: 详情请见laravel-log");
    }
}
