<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;
use Modules\Lease\Models\BlServiceStock;
use Modules\Manage\Models\Service\ServiceStock;

class SyncServiceStock implements ShouldQueue
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
            $maxCreatedTime = ServiceStock::max("created_at");
            $maxUpdatedTime = ServiceStock::max("updated_at");
            $agents = leaseAgentCache();
            BlServiceStock::where(function ($query) use ($maxCreatedTime, $maxUpdatedTime) {
                //查询最近一小时新增或更新的合约订单
                if ($maxCreatedTime && $maxUpdatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime)
                        ->orWhere("updated_at", ">", $maxUpdatedTime);
                } elseif ($maxCreatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime);
                } elseif ($maxUpdatedTime) {
                    $query->where("updated_at", ">", $maxUpdatedTime);
                }
            })->chunk(100, function ($stocks) use ($agents) {
                foreach ($stocks as $stockData) {
                    $stock = ServiceStock::find($stockData->id);
                    if (!$stock) {
                        $stock = new ServiceStock();
                    }
                    $stock->id = $stockData->id;
                    $stock->service_id = $stockData->service_id;
                    $stock->model_id = $stockData->model_id;
                    $stock->model_name = $stockData->model_name;
                    $stock->lease_type = $stockData->lease_type;
                    $stock->sku = $stockData->sku;
                    $stock->battery_type = $stockData->battery_type;
                    $stock->created_at = $stockData->created_at;
                    $stock->updated_at = $stockData->updated_at;
                    $stock->deleted_at = $stockData->deleted_at;
                    $stock->agent_id = $stockData->agent_id;
                    if ($agent = Arr::get($agents, $stockData->agent_id)) {
                        $stock->county_id = $agent['county_id'];
                        $stock->city_id = $agent['city_id'];
                        $stock->province_id = $agent['province_id'];
                    }
                    $stock->save();
                }
            });
            systemLog("同步租点网点库存成功");
        } catch (\Exception $e) {
            \Log::error("同步租点网点库存失败: {$e->getMessage()}");
            systemLog("同步租点网点库存失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步租点网点库存失败：{$exception->getMessage()}");
        systemLog("同步租点网点库存失败: 详情请见laravel-log");
    }
}
