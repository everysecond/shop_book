<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2020/2/19 9:42
 */

namespace Modules\Manage\Services;

use \Exception as Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Lease\Models\BlBatteryGroupModel;
use Modules\Lease\Models\BlLogisticsStockLog;
use Modules\Lease\Models\BlServiceStockLog;
use Modules\Lease\Models\BlSystemConfig;
use Modules\Manage\Models\Report\LeaseLogisticsStockLog;
use Modules\Manage\Models\Report\LeaseServiceStockLog;

class SyncJobService
{
    //同步整理网点库存日志
    public function syncLeaseServiceStockLog()
    {
        $maxCreatedTime = LeaseServiceStockLog::query()->max("created_at");
        $provinceArr    = leaseAgentCache(true);
        $modelGroupArr  = BlBatteryGroupModel::groupParams();
        $modelParams    = Arr::get($modelGroupArr, 'model');
        $groupParams    = Arr::get($modelGroupArr, 'group');
        $config         = BlSystemConfig::query()->where('key', 'recycle_price')->first();
        $config         = $config ? $config->value : 0;
        $logTypeArr     = LeaseServiceStockLog::LOG_TYPE_ARR;
        DB::connection()->disableQueryLog();  //禁用query log
        BlServiceStockLog::query()->where(function ($query) use ($maxCreatedTime) {
            if ($maxCreatedTime) {
                $query->where("created_at", ">", $maxCreatedTime);
            }
        })->chunk(100, function ($logs) use ($provinceArr, $modelParams, $groupParams, $logTypeArr, $config) {
            $logs   = $logs->toArray();
            $logArr = [];
            foreach ($logs as $log) {
                if ($log['lease_type'] == 0) {
                    $modelName = Arr::get($modelParams, $log['model_id'] . ".name", '');
                    $weight    = $log['num'] * Arr::get($modelParams, $log['model_id'] . ".weight", 0);
                    $price     = $log['num'] * $config;
                } else {
                    $modelName = Arr::get($groupParams, $log['model_id'] . ".name", '');
                    $weight    = $log['num'] * Arr::get($groupParams, $log['model_id'] . ".weight", 0);
                    $price     = $log['num'] * Arr::get($groupParams, $log['model_id'] . ".price", 0);
                }

                $logType = logType($log['relation_type'], $log['lease_type']);

                $logArr[] = [
                    'province_id'   => $log['agent_id'] > 0 ?
                        (isset($provinceArr[$log['agent_id']]) ? $provinceArr[$log['agent_id']]['province_id'] : 0)
                        : 0,
                    'id'            => $log['id'],
                    'service_id'    => $log['service_id'],
                    'model_id'      => $log['model_id'],
                    'model_name'    => $modelName,
                    'lease_type'    => $log['lease_type'],
                    'num'           => $log['num'],
                    'weight'        => $weight,
                    'price'         => $price,
                    'battery_type'  => $log['battery_type'],
                    'stock_type'    => $log['stock_type'],
                    'relation_type' => $log['relation_type'],
                    'relation_id'   => $log['relation_id'],
                    'created_at'    => $log['created_at'],
                    'log_type'      => $logType,
                    'log_type_txt'  => Arr::get($logTypeArr, $logType),
                    'agent_id'      => $log['agent_id']
                ];
            }
            LeaseServiceStockLog::query()->insert($logArr);
            $logArr = null;
            $logs   = null;
        });
    }

    //同步整理网点库存日志
    public function syncLeaseLogisticsStockLog()
    {
        $maxCreatedTime = LeaseLogisticsStockLog::query()->max("created_at");
        $provinceArr    = leaseAgentCache(true);
        $modelGroupArr  = BlBatteryGroupModel::groupParams();
        $modelParams    = Arr::get($modelGroupArr, 'model');
        $groupParams    = Arr::get($modelGroupArr, 'group');
        $config         = BlSystemConfig::query()->where('key', 'recycle_price')->first();
        $config         = $config ? $config->value : 0;
        $logTypeArr     = LeaseLogisticsStockLog::LOG_TYPE_ARR;
        DB::connection()->disableQueryLog();  //禁用query log
        BlLogisticsStockLog::query()->where(function ($query) use ($maxCreatedTime) {
            if ($maxCreatedTime) {
                $query->where("created_at", ">", $maxCreatedTime);
            }
        })->chunk(100, function ($logs) use ($provinceArr, $modelParams, $groupParams, $logTypeArr, $config) {
            $logs   = $logs->toArray();
            $logArr = [];
            foreach ($logs as $log) {
                if ($log['lease_type'] == 0) {
                    $modelName = Arr::get($modelParams, $log['model_id'] . ".name", '');
                    $weight    = $log['num'] * Arr::get($modelParams, $log['model_id'] . ".weight", 0);
                    $price     = $log['num'] * $config;
                } else {
                    $modelName = Arr::get($groupParams, $log['model_id'] . ".name", '');
                    $weight    = $log['num'] * Arr::get($groupParams, $log['model_id'] . ".weight", 0);
                    $price     = $log['num'] * Arr::get($groupParams, $log['model_id'] . ".price", 0);
                }

                $logType = logisticsLogType($log['relation_type'], $log['lease_type']);

                $logArr[] = [
                    'province_id'   => $log['agent_id'] > 0 ?
                        (isset($provinceArr[$log['agent_id']]) ? $provinceArr[$log['agent_id']]['province_id'] : 0)
                        : 0,
                    'id'            => $log['id'],
                    'logistics_id'  => $log['logistics_id'],
                    'model_id'      => $log['model_id'],
                    'model_name'    => $modelName,
                    'lease_type'    => $log['lease_type'],
                    'num'           => $log['num'],
                    'weight'        => $weight,
                    'price'         => $price,
                    'battery_type'  => $log['battery_type'],
                    'stock_type'    => $log['stock_type'],
                    'type'          => $log['type'],
                    'relation_type' => $log['relation_type'],
                    'relation_id'   => $log['relation_id'],
                    'created_at'    => $log['created_at'],
                    'log_type'      => $logType,
                    'log_type_txt'  => Arr::get($logTypeArr, $logType),
                    'agent_id'      => $log['agent_id']
                ];
            }
            LeaseLogisticsStockLog::query()->insert($logArr);
            $logArr = null;
            $logs   = null;
        });
    }

}