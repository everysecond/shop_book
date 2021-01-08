<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlAppDown;
use Modules\Manage\Models\Report\LeaseAppDownLog;

class GenerateAppDownLog implements ShouldQueue
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
            $maxDate = LeaseAppDownLog::max("date");
            $begin = $end = date("Y-m-d");
            if (!$maxDate) {
                $firstLog = BlAppDown::query()->min("day");
                $begin = $firstLog ? date("Y-m-d", strtotime($firstLog)) : $begin;
            } else {
                $begin = $maxDate;
                LeaseAppDownLog::whereBetween("date", [$begin, $end])->delete();
            }
            $dataRange = getDateRange($begin, $end);
            $logs = [];
            foreach ($dataRange as $date) {
                $data = BlAppDown::query()->selectRaw('count(id) as num,app_type,channel')
                    ->where('day', date('Ymd', strtotime($date)))
                    ->groupBy('app_type')
                    ->groupBy('channel')
                    ->get();
                $appTypeOne = [];
                $appTypeTwo = [];
                $total1 = 0;
                $total2 = 0;
                foreach ($data as $datum) {
                    if ($datum->app_type == 1) {
                        $appTypeOne[$datum->channel] = $datum->num;
                        $total1 += $datum->num;
                    } elseif ($datum->app_type == 2) {
                        $appTypeTwo[$datum->channel] = $datum->num;
                        $total2 += $datum->num;
                    }
                }
                $appOne = [
                    'date'         => $date,
                    'app_type'     => 1,
                    'total'        => $total1,
                    'channel_json' => json_encode($appTypeOne)
                ];
                $logs[] = $appOne;
                $appTwo = [
                    'date'         => $date,
                    'app_type'     => 2,
                    'total'        => $total2,
                    'channel_json' => json_encode($appTypeTwo)
                ];
                $logs[] = $appTwo;
                if (count($logs) > 100) {
                    LeaseAppDownLog::insert($logs);
                    $logs = [];
                }
            }
            LeaseAppDownLog::insert($logs);
            systemLog("生成app下载统计日志成功");
        } catch (\Exception $e) {
            \Log::error("生成app下载统计日志失败: {$e->getMessage()}");
            systemLog("生成app下载统计日志失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成app下载统计日志失败：{$exception->getMessage()}");
        systemLog("生成app下载统计日志失败: 详情请见laravel-log");
    }
}
