<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Modules\Lease\Models\BlServiceStockLog;
use Modules\Manage\Models\Service\LeaseServiceStockLogInfo;

class SyncServiceStockLog implements ShouldQueue
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
            $maxCreatedTime = LeaseServiceStockLogInfo::query()->max("created_at");
            $maxUpdatedTime = LeaseServiceStockLogInfo::query()->max("updated_at");
            $provinceArr = leaseAgentCache(true);
            DB::connection()->disableQueryLog();  //禁用query log
            BlServiceStockLog::query()->where(function ($query) use ($maxCreatedTime, $maxUpdatedTime) {
                if ($maxCreatedTime && $maxUpdatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime)
                        ->orWhere("updated_at", ">", $maxUpdatedTime);
                } elseif ($maxCreatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime);
                } elseif ($maxUpdatedTime) {
                    $query->where("updated_at", ">", $maxUpdatedTime);
                }

            })->chunk(100, function ($logs) use ($maxCreatedTime, $provinceArr) {
                if ($maxCreatedTime) {
                    foreach ($logs as $log) {
                        $syncLog = LeaseServiceStockLogInfo::query()->find($log->id);
                        if (!$syncLog) {
                            $syncLog = new LeaseServiceStockLogInfo();
                        }
                        $syncLog->id = $log->id;
                        $syncLog->service_id = $log->service_id;
                        $syncLog->model_id = $log->model_id;
                        $syncLog->lease_type = $log->lease_type;
                        $syncLog->sku_before = $log->sku_before;
                        $syncLog->num = $log->num;
                        $syncLog->sku_after = $log->sku_after;
                        $syncLog->battery_type = $log->battery_type;
                        $syncLog->stock_type = $log->stock_type;
                        $syncLog->relation_type = $log->relation_type;
                        $syncLog->relation_id = $log->relation_id;
                        $syncLog->remark = $log->remark;
                        $syncLog->created_at = $log->created_at;
                        $syncLog->updated_at = $log->updated_at;
                        $syncLog->deleted_at = $log->deleted_at;
                        $syncLog->agent_id = $log->agent_id;
                        $syncLog->date = date('Y-m-d', strtotime($log->created_at));
                        $syncLog->province_id = $log->agent_id > 0 ?
                            (isset($provinceArr[$log->agent_id]) ? $provinceArr[$log->agent_id]['province_id'] : 0) : 0;
                        $syncLog->save();
                        $syncLog = null;
                    }
                } else {
                    $logs = $logs->toArray();
                    $logArr = [];
                    foreach ($logs as $log) {
                        $logArr[] = [
                            'date'          => date('Y-m-d', strtotime($log['created_at'])),
                            'province_id'   => $log['agent_id'] > 0 ?
                                (isset($provinceArr[$log['agent_id']]) ? $provinceArr[$log['agent_id']]['province_id'] : 0)
                                : 0,
                            'id'            => $log['id'],
                            'service_id'    => $log['service_id'],
                            'model_id'      => $log['model_id'],
                            'lease_type'    => $log['lease_type'],
                            'sku_before'    => $log['sku_before'],
                            'num'           => $log['num'],
                            'sku_after'     => $log['sku_after'],
                            'battery_type'  => $log['battery_type'],
                            'stock_type'    => $log['stock_type'],
                            'relation_type' => $log['relation_type'],
                            'relation_id'   => $log['relation_id'],
                            'remark'        => $log['remark'],
                            'created_at'    => $log['created_at'],
                            'updated_at'    => $log['updated_at'],
                            'deleted_at'    => $log['deleted_at'],
                            'agent_id'      => $log['agent_id']
                        ];
                    }
                    LeaseServiceStockLogInfo::query()->insert($logArr);
                    $logArr = null;
                }
                $logs = null;
            });
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
