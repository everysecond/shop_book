<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlFlow;
use Modules\Manage\Models\Service\LeaseEventFlow;
use Modules\Manage\Models\Service\LeaseEventLog;

class GenerateEventLog implements ShouldQueue
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
            $maxDate = LeaseEventLog::query()->max("day");
            $begin = $end = date("Ymd");
            if (!$maxDate) {
                $firstLog = LeaseEventFlow::query()
                    ->whereIn("page_url", config("global.active_event_url"))
                    ->whereIn("app_type", [BlFlow::APP_TYPE_THREE, BlFlow::APP_TYPE_FOUR])
                    ->min("day");
                $begin = $firstLog ? $firstLog : $begin;
            } else {
                $begin = $maxDate;
                LeaseEventLog::query()->whereBetween("day", [$begin, $end])->delete();
            }

            $times = "sum(times) as times";
            $userNum = "count(DISTINCT user_id) as user_num";

            LeaseEventFlow::query()->selectRaw("page_url,$times,$userNum,day,province_id,date")
                ->whereIn("page_url", config("global.active_event_url"))
                ->whereIn("app_type", [BlFlow::APP_TYPE_THREE, BlFlow::APP_TYPE_FOUR])
                ->whereBetween("day", [$begin, $end])
                ->groupBy("page_url")
                ->groupBy("province_id")
                ->groupBy("day")
                ->chunk(100, function ($logs) {
                    $syncLogs = [];
                    foreach ($logs as $log) {
                        $syncLog = [
                            "page_url"    => $log->page_url,
                            "date"        => $log->date,
                            "day"         => $log->day,
                            "times"       => $log->times,
                            "user_num"    => $log->user_num,
                            "province_id" => $log->province_id
                        ];
                        $syncLogs[] = $syncLog;
                    }
                    LeaseEventLog::query()->insert($syncLogs);
                });
            systemLog("生成活跃事件二次处理日志任务成功");
        } catch (\Exception $e) {
            \Log::error("生成活跃事件二次处理日志失败: {$e->getMessage()}");
            systemLog("生成活跃事件二次处理日志失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成活跃事件二次处理日志失败：{$exception->getMessage()}");
        systemLog("生成活跃事件二次处理日志失败: 详情请见laravel-log");
    }
}
