<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlFlow;
use Modules\Lease\Models\BlUser;
use Modules\Manage\Models\Service\LeaseCusEventFlow;

class GenerateCusEventFlow implements ShouldQueue
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
            $maxDate = LeaseCusEventFlow::query()->max("day");
            $begin = $end = date("Ymd");
            if (!$maxDate) {
                $firstLog = BlFlow::query()
                    ->whereIn("url_name", config("global.user_active_event_url"))
                    ->whereIn("app_type", [BlFlow::APP_TYPE_ONE, BlFlow::APP_TYPE_TWO])
                    ->min("day");
                $begin = $firstLog ? $firstLog : $begin;
            } else {
                $begin = $maxDate;
                LeaseCusEventFlow::query()->whereIn("app_type", [BlFlow::APP_TYPE_ONE, BlFlow::APP_TYPE_TWO])
                    ->whereBetween("day", [$begin, $end])->delete();
            }
            $userArr = BlUser::query()->pluck('province_id', 'id')->toArray();

            BlFlow::query()->selectRaw("*,COUNT(id) as times")
                ->whereIn("url_name", config("global.user_active_event_url"))
                ->whereIn("app_type", [BlFlow::APP_TYPE_ONE, BlFlow::APP_TYPE_TWO])
                ->whereBetween("day", [$begin, $end])
                ->groupBy("url_name")
                ->groupBy("user_id")
                ->groupBy("day")
                ->chunk(3000, function ($logs) use ($userArr) {
                    $syncFlows = [];
                    foreach ($logs as $log) {
                        $provinceId = isset($userArr[$log->user_id]) ? $userArr[$log->user_id] : 0;
                        $syncFlow = [
                            "date"        => date("Y-m-d", strtotime($log->created_at)),
                            "year"        => $log->year,
                            "month"       => $log->month,
                            "day"         => $log->day,
                            "client_ip"   => $log->client_ip,
                            "app_type"    => $log->app_type,
                            "page_url"    => $log->page_url,
                            "url_name"    => $log->url_name,
                            "system_type" => $log->system_type,
                            "version"     => $log->version,
                            "hour"        => $log->hour,
                            "user_id"     => $log->user_id,
                            "province_id" => $provinceId,
                            "times"       => $log->times
                        ];
                        $syncFlows[] = $syncFlow;
                    }
                    LeaseCusEventFlow::query()->insert($syncFlows);
                });
            systemLog("生成C端活跃事件日志任务成功!");
        } catch (\Exception $e) {
            \Log::error("生成C端活跃事件日志失败: {$e->getMessage()}");
            systemLog("生成C端活跃事件日志失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成C端活跃事件日志失败：{$exception->getMessage()}");
        systemLog("生成C端活跃事件日志失败: 详情请见laravel-log");
    }
}
