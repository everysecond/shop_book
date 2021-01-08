<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Manage\Models\Report\LeaseBatteryLog;
use Modules\Manage\Models\Report\LeaseContract;

class GenerateBatteryLog implements ShouldQueue
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
            LeaseBatteryLog::truncate();
            $allProvinces = allLeaseProvinces();
            LeaseContract::provinceId(0,"")->chunk(100, function ($items) {
                $items = $items->toArray();
                LeaseBatteryLog::insert($items);
            });

            foreach ($allProvinces as $provinceId=>$province) {
                LeaseContract::provinceId($provinceId)->chunk(100, function ($items) {
                    $items = $items->toArray();
                    LeaseBatteryLog::insert($items);
                });
            }
            systemLog("生成租赁合约电池型号统计表任务成功");
        } catch (\Exception $e) {
            \Log::error("生成租赁合约电池型号统计表失败: {$e->getMessage()}");
            systemLog("生成租赁合约电池型号统计表失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成租赁合约电池型号统计表失败：{$exception->getMessage()}");
        systemLog("生成租赁合约电池型号统计表失败: 详情请见laravel-log");
    }
}
