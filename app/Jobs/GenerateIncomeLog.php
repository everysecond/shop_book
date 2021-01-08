<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Manage\Models\Service\LeaseIncomeLog;
use Modules\Manage\Models\Service\LeaseServiceBalanceLog;
use Modules\Manage\Repositories\Report\LeaseServiceRepository;

class GenerateIncomeLog implements ShouldQueue
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
            $maxDate = LeaseIncomeLog::max("date");
            $begin = $end = date("Y-m-d");
            if (!$maxDate) {
                $firstLog = LeaseServiceBalanceLog::query()
                    ->where("source", LeaseServiceBalanceLog::SOURCE_FOUR)
                    ->min("created_at");
                $begin = $firstLog ? date("Y-m-d", strtotime($firstLog)) : $begin;
            } else {
                $begin = $maxDate;
                LeaseIncomeLog::whereBetween("date", [$begin, $end])->delete();
            }

            $data = app(LeaseServiceRepository::class)->getIncomeAreaByDate([$begin, $end]);
            $dataRange = getDateRange($begin, $end);
            $logs = [];
            foreach ($dataRange as $date) {
                $log = ["date" => $date];
                $json = [];
                $total = 0;
                foreach ($data as $datum) {
                    if ($datum["date"] == $date) {
                        $json[$datum["province_id"]] = $datum["income"];
                        $total += $datum["income"];
                    }
                }
                $log["json"] = json_encode($json);
                $log["total"] = $total;
                $logs[] = $log;
                if (count($logs) > 100) {
                    LeaseIncomeLog::insert($logs);
                    $logs = [];
                }
            }
            LeaseIncomeLog::insert($logs);
            systemLog("生成网点收益统计日志任务成功");
        } catch (\Exception $e) {
            \Log::error("生成网点收益统计日志失败: {$e->getMessage()}");
            systemLog("生成网点收益统计日志失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成网点收益统计日志失败：{$exception->getMessage()}");
        systemLog("生成网点收益统计日志失败: 详情请见laravel-log");
    }
}
