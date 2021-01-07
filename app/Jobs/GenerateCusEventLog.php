<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlFlow;
use Modules\Manage\Models\Service\LeaseCusEventFlow;
use Modules\Manage\Models\Service\LeaseCusEventLog;

class GenerateCusEventLog implements ShouldQueue
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
            $maxDate = LeaseCusEventLog::query()->max("day");
            $begin = $end = date("Ymd");
            if (!$maxDate) {
                $firstLog = LeaseCusEventFlow::query()
                    ->whereIn("url_name", config("global.user_active_event_url"))
                    ->whereIn("app_type", [BlFlow::APP_TYPE_ONE, BlFlow::APP_TYPE_TWO])
                    ->min("day");
                $begin = $firstLog ? $firstLog : $begin;
            } else {
                $begin = $maxDate;
                LeaseCusEventLog::query()->whereBetween("day", [$begin, $end])->delete();
            }

            $times = "sum(times) as times";
            $userNum = "count(DISTINCT user_id) as user_num";

            LeaseCusEventFlow::query()->selectRaw("page_url,url_name,$times,$userNum,day,province_id,date")
                ->whereIn("url_name", config("global.user_active_event_url"))
                ->whereIn("app_type", [BlFlow::APP_TYPE_ONE, BlFlow::APP_TYPE_TWO])
                ->whereBetween("day", [$begin, $end])
                ->groupBy("url_name")
                ->groupBy("province_id")
                ->groupBy("day")
                ->chunk(100, function ($logs) {
                    $syncLogs = [];
                    foreach ($logs as $log) {
                        $syncLog = [
                            "page_url"    => $log->page_url,
                            "url_name"    => $log->url_name,
                            "date"        => $log->date,
                            "day"         => $log->day,
                            "times"       => $log->times,
                            "user_num"    => $log->user_num,
                            "province_id" => $log->province_id
                        ];
                        $syncLogs[] = $syncLog;
                    }
                    LeaseCusEventLog::query()->insert($syncLogs);
                });
            systemLog("生成C端活跃事件二次处理日志任务成功");
        } catch (\Exception $e) {
            \Log::error("生成C端活跃事件二次处理日志失败: {$e->getMessage()}");
            systemLog("生成C端活跃事件二次处理日志失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成C端活跃事件二次处理日志失败：{$exception->getMessage()}");
        systemLog("生成C端活跃事件二次处理日志失败: 详情请见laravel-log");
    }
}
