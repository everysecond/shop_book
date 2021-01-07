<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlFlow;
use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Service\LeaseEventFlow;

class GenerateEventFlow implements ShouldQueue
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
            $maxDate = LeaseEventFlow::query()->max("day");
            $begin = $end = date("Ymd");
            if (!$maxDate) {
                $firstLog = BlFlow::query()
                    ->whereIn("page_url", config("global.active_event_url"))
                    ->whereIn("app_type", [BlFlow::APP_TYPE_THREE, BlFlow::APP_TYPE_FOUR])
                    ->min("day");
                $begin = $firstLog ? $firstLog : $begin;
            } else {
                $begin = $maxDate;
                LeaseEventFlow::query()->whereIn("app_type", [BlFlow::APP_TYPE_THREE, BlFlow::APP_TYPE_FOUR])
                    ->whereBetween("day", [$begin, $end])->delete();
            }

            $serviceUserArr = LeaseService::query()->pluck('province_id', 'id')->toArray();
            BlFlow::query()->selectRaw("*,COUNT(id) as times")
//                ->with("serviceUser")
                ->whereIn("page_url", config("global.active_event_url"))
                ->whereIn("app_type", [BlFlow::APP_TYPE_THREE, BlFlow::APP_TYPE_FOUR])
                ->whereBetween("day", [$begin, $end])
                ->groupBy("page_url")
                ->groupBy("user_id")
                ->groupBy("day")
                ->chunk(3000, function ($logs) use ($serviceUserArr) {
                    $syncFlows = [];
                    foreach ($logs as $log) {
//                        $provinceId = $log->serviceUser && $log->serviceUser->agent_id
//                            ? (isset(getProvinceId($log->serviceUser->agent_id)["id"])
//                                ? getProvinceId($log->serviceUser->agent_id)["id"] : 0) : 0;
                        $provinceId = isset($serviceUserArr[$log->user_id]) ? $serviceUserArr[$log->user_id] : 0;
                        $syncFlow = [
                            "date"        => date("Y-m-d", strtotime($log->created_at)),
                            "year"        => $log->year,
                            "month"       => $log->month,
                            "day"         => $log->day,
                            "client_ip"   => $log->client_ip,
                            "app_type"    => ($log->page_url == "bonus-calculate") ? 4 : $log->app_type,
                            "page_url"    => $log->page_url,
                            "system_type" => $log->system_type,
                            "version"     => $log->version,
                            "hour"        => $log->hour,
                            "user_id"     => $log->user_id,
                            "province_id" => $provinceId,
                            "times"       => $log->times
                        ];
                        $syncFlows[] = $syncFlow;
                    }
                    LeaseEventFlow::query()->insert($syncFlows);
                });
            systemLog("生成活跃事件日志任务成功");
        } catch (\Exception $e) {
            \Log::error("生成活跃事件日志失败: {$e->getMessage()}");
            systemLog("生成活跃事件日志失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成活跃事件日志失败：{$exception->getMessage()}");
        systemLog("生成活跃事件日志失败: 详情请见laravel-log");
    }
}
