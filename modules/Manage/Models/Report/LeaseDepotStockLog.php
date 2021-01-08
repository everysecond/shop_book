<?php

namespace Modules\Manage\Models\Report;

use Modules\Lease\Models\BlDepotStockLog;
use Modules\Manage\Models\Model;

class LeaseDepotStockLog extends Model
{
    public function syncLeaseDepotStockLog(){

        try {
            $maxCreatedTime = LeaseDepotStockLog::max("created_at");
            $maxUpdatedTime = LeaseDepotStockLog::max("updated_at");

            BlDepotStockLog::where(function ($query) use ($maxCreatedTime, $maxUpdatedTime) {
                //查询最近一小时新增或更新的合约订单
                if ($maxCreatedTime && $maxUpdatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime)
                        ->orWhere("updated_at", ">", $maxUpdatedTime);
                } elseif ($maxCreatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime);
                } elseif ($maxUpdatedTime) {
                    $query->where("updated_at", ">", $maxUpdatedTime);
                }
            })->chunk(100, function ($stocks){
                foreach ($stocks as $stockData) {
                    $stock = LeaseDepotStockLog::find($stockData->id);
                    if (!$stock) {
                        $stock = new LeaseDepotStockLog();
                    }


                    $stock->id = $stockData->id;
                    $stock->depot_id = $stockData->depot_id;
                    $stock->model_id = $stockData->model_id;
                    $stock->lease_type = $stockData->lease_type;
                    $stock->sku_before = $stockData->sku_before;
                    $stock->num = $stockData->num;
                    $stock->sku_after = $stockData->sku_after;
                    $stock->battery_type = $stockData->battery_type;
                    $stock->stock_type = $stockData->stock_type;
                    $stock->type = $stockData->type;
                    $stock->remark = $stockData->remark;
                    $stock->created_at = $stockData->created_at;
                    $stock->updated_at = $stockData->updated_at;
                    $stock->deleted_at = $stockData->deleted_at;
                    $stock->relation_type = $stockData->relation_type;
                    $stock->relation_id = $stockData->relation_id;
                    $stock->model_name = $stockData->model_name;

                    if ($stockData->model_id>1000){
                        $modlesG = BlBatteryGroupModele::find($stockData->model_id);
                        if ($modlesG){
                            $modles = BlBatteryModele::find($modlesG->model_id);
                            if ($modles){
                                $stock->price = ($stockData->num)*($modlesG->price);
                                $stock->weight = ($stockData->num)*($modlesG->num)*($modles->weight);
                            }
                        }
                    }else{
                        $modles = BlBatteryModele::find($stockData->model_id);
                        if ($modles){
                            $stock->price = ($stockData->num)*($modles->price);
                            $stock->weight = ($stockData->num)*($modles->weight);
                        }
                    }
                    $stock->top_id = $stockData->top_id;
                    $stock->save();
                }
            });
            systemLog("同步仓库库存日志成功");
        } catch (\Exception $e) {
            \Log::error("同步仓库库存日志失败: {$e->getMessage()}");
            systemLog("同步仓库库存日志失败: 详情请见laravel-log");
        }

    }
}
