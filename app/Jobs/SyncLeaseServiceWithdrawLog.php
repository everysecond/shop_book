<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Manage\Models\Service\LeaseIncomeLog;
use Modules\Manage\Models\Service\LeaseServiceBalanceLog;
use Modules\Manage\Models\Service\LeaseServiceWithdraw;
use Modules\Manage\Models\Service\LeaseServiceWithdrawLog;
use Modules\Manage\Repositories\Report\LeaseServiceRepository;

class SyncLeaseServiceWithdrawLog implements ShouldQueue
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

            $maxDate = LeaseServiceWithdrawLog::max("date");

            $begin = $end = date("Y-m-d");
            if (!$maxDate) {
                $firstLog = LeaseServiceWithdraw::query()
                    ->min("created_at");
                $begin = $firstLog ? date("Y-m-d", strtotime($firstLog)) : $begin;
            } else {
                $begin = $maxDate;
                LeaseServiceWithdrawLog::whereBetween("date", [$begin, $end])->delete();
            }


            $defaultDay = [
                "begin" => $begin,
                "end"   => $end . " 23:59:59"
            ];
            $data = LeaseServiceWithdraw::selectRaw("sum(amount) as amount, province_id,DATE_FORMAT(created_at, '%Y-%m-%d') as date,count(id) as num")
                ->whereBetween("created_at", $defaultDay)
                ->groupBy("province_id")
                ->groupBy("date")
                ->orderBy("province_id")
                ->orderBy("date")
                ->get()
                ->toArray();

            $dataRange = getDateRange($begin, $end);
            $logs = [];
            foreach ($dataRange as $date) {
                $log = ["date" => $date];
                $json_amount = $json_num = [];
                $total = 0;
                foreach ($data as $datum) {
                    if ($datum["date"] == $date) {
                        $json_amount[$datum["province_id"]] = $datum["amount"];
                        $json_num[$datum["province_id"]] = $datum["num"];
                        $total += $datum["amount"];
                    }
                }
                $log["json_amount"] = json_encode($json_amount);
                $log["total"] = $total;
                $log["json_num"] = json_encode($json_num);
                $logs[] = $log;
                if (count($logs) > 100) {
                    LeaseServiceWithdrawLog::insert($logs);
                    $logs = [];
                }
            }
            LeaseServiceWithdrawLog::insert($logs);
            systemLog("生成网点提现统计日志任务成功");
        } catch (\Exception $e) {
            \Log::error("生成网点提现统计日志失败: {$e->getMessage()}");
            systemLog("生成网点提现统计日志失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成网点提现统计日志失败：{$exception->getMessage()}");
        systemLog("生成网点提现统计日志失败: 详情请见laravel-log");
    }
}
