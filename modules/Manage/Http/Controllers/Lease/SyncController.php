<?php

namespace Modules\Manage\Http\Controllers\Lease;

use Illuminate\Routing\Controller;
use Modules\Manage\Services\SyncJobService;

class SyncController extends Controller
{
    protected $syncService;

    public function __construct(SyncJobService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function serviceStockLog()
    {
        try {
            $this->syncService->syncLeaseServiceStockLog();
            systemLog("同步网点库存日志成功");
            return result('', 1, '同步成功！');
        } catch (\Exception $e) {
            \Log::error("同步网点库存日志失败: {$e->getMessage()}");
            systemLog("同步网点库存日志失败: 详情请见laravel-log");
            return result($e->getMessage(), -1);
        }
    }

    public function syncLeaseLogisticsStockLog()
    {
        try {
            $this->syncService->syncLeaseLogisticsStockLog();
            systemLog("同步物流库存日志成功");
            return result('', 1, '同步成功！');
        } catch (\Exception $e) {
            \Log::error("同步物流库存日志失败: {$e->getMessage()}");
            systemLog("同步物流库存日志失败: 详情请见laravel-log");
            return result($e->getMessage(), -1);
        }
    }
}
