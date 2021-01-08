<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlServiceStock;
use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Service\LeaseIncomeLog;
use Modules\Manage\Models\Service\LeaseServiceBalanceLog;
use Modules\Manage\Models\Service\LeaseServiceStockLogInfo;
use Modules\Manage\Models\Service\ServiceStockLog;
use Modules\Manage\Repositories\Report\LeaseServiceRepository;

class GenerateServiceStockLog implements ShouldQueue
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
            //租赁类型电池型号
            $batteryTypeOne = BlServiceStock::query()
                ->where('battery_type', LeaseServiceStockLogInfo::BATTERY_TYPE_ONE)
                ->groupBy('model_name')->pluck('model_name')->toArray();
            //废旧电池型号
            $batteryTypeTwo = BlServiceStock::query()
                ->where('battery_type', LeaseServiceStockLogInfo::BATTERY_TYPE_TWO)
                ->groupBy('model_name')->pluck('model_name')->toArray();
            //退回电池型号
            $batteryTypeThree = BlServiceStock::query()
                ->where('battery_type', LeaseServiceStockLogInfo::BATTERY_TYPE_THREE)
                ->groupBy('model_name')->pluck('model_name')->toArray();
            $batteryTypeArr = [
                LeaseServiceStockLogInfo::LEASE_TYPE_ONE,//全新
                LeaseServiceStockLogInfo::LEASE_TYPE_TWO,//备用
                LeaseServiceStockLogInfo::LEASE_TYPE_FOUR,//退回
                LeaseServiceStockLogInfo::LEASE_TYPE_ZERO//回收废旧
            ];
            $mode = ['total' => 0];
            foreach ($batteryTypeArr as $item) {
                $mode[$item] = ['total' => 0];
                if ($item == 4) {
                    $modelArr = $batteryTypeThree;
                } elseif ($item == 0) {
                    $modelArr = $batteryTypeTwo;
                } else {
                    $modelArr = $batteryTypeOne;
                }
                foreach ($modelArr as $modelName) {
                    $mode[$item][$modelName] = 0;
                }
            }
            $log = ['total' => $mode];
            foreach (allLeaseProvinces() as $id => $province) {
                $log[$id] = $mode;
            }
            $servicesArr = LeaseService::query()->pluck('province_id', 'id')->toArray();
            BlServiceStock::query()->chunk(300, function ($stocks) use (&$log, $servicesArr) {
                foreach ($stocks as $stock) {
                    $log['total']['total'] += $stock->sku;
                    $log['total'][$stock->lease_type]['total'] += $stock->sku;
                    $log['total'][$stock->lease_type][$stock->model_name] += $stock->sku;
                    if (isset($servicesArr[$stock->service_id]) && ($pid = $servicesArr[$stock->service_id])) {
                        $log[$pid]['total'] += $stock->sku;
                        $log[$pid][$stock->lease_type]['total'] += $stock->sku;
                        $log[$pid][$stock->lease_type][$stock->model_name] += $stock->sku;
                    }
                }
            });
            $date = date('Y-m-d', strtotime('-1 day'));
            $syncLog = ServiceStockLog::query()->where('date', $date)->first();
            if (!$syncLog) {
                $syncLog = new ServiceStockLog();
            }
            $syncLog->date = $date;
            $syncLog->json_data = json_encode($log);
            $syncLog->save();
            systemLog("生成网点库存统计日志成功");
        } catch (\Exception $e) {
            \Log::error("生成网点库存统计日志失败: {$e->getMessage()}");
            systemLog("生成网点库存统计日志: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成网点库存统计日志失败：{$exception->getMessage()}");
        systemLog("生成网点库存统计日志失败: 详情请见laravel-log");
    }
}
