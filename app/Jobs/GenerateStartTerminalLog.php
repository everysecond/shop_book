<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlFlow;
use Modules\Manage\Models\Report\LeaseStartTerminalLog;

class GenerateStartTerminalLog implements ShouldQueue
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
            $data = BlFlow::selectRaw("COUNT(id) as num,DATE_FORMAT(created_at, '%Y-%m-%d') as date,app_type")
                ->where("page_url","slide/bootstrap")
                ->groupBy("date", "app_type");
            $logModel = [
                "total"               => 0,
                "user_ios_num"        => 0,
                "user_android_num"    => 0,
                "web_ios_num"         => 0,
                "web_android_num"     => 0,
                "depot_android_num"   => 0,
                "flow_android_num"    => 0,
                "factory_android_num" => 0,
                "created_at"          => time(),
                "updated_at"          => time()
            ];
            $flowData = [];
            $maxDate = LeaseStartTerminalLog::max("date");
            $begin = $end = date("Y-m-d");
            if (!$maxDate) {
                $flowData = $data->get()->toArray();
                $begin = isset($flowData[0]) ? $flowData[0]["date"] : $begin;
            } else {
                $begin = $maxDate;
                LeaseStartTerminalLog::whereBetween("date", [$begin, $end])->delete();
                $flowData = $data->where("created_at", ">", $begin)->get()->toArray();
            }
            $dateArr = getDateRange($begin, $end);
            $logs = [];
            foreach ($dateArr as $date) {
                $loginfo = $logModel;
                $loginfo["date"] = $date;
                $totalNum = 0;
                foreach ($flowData as $item) {
                    if ($item["date"] == $date) {
                        $totalNum += $item["num"];
                        if($item["app_type"] == 1){
                            $loginfo["user_ios_num"] = $item["num"];
                        } elseif($item["app_type"] == 2){
                            $loginfo["user_android_num"] = $item["num"];
                        } elseif($item["app_type"] == 3){
                            $loginfo["web_ios_num"] = $item["num"];
                        } elseif($item["app_type"] == 4){
                            $loginfo["web_android_num"] = $item["num"];
                        } elseif($item["app_type"] == 5){
                            $loginfo["depot_android_num"] = $item["num"];
                        } elseif($item["app_type"] == 6){
                            $loginfo["flow_android_num"] = $item["num"];
                        } elseif($item["app_type"] == 7){
                            $loginfo["factory_android_num"] = $item["num"];
                        }
                    }
                }
                $loginfo["total"] = $totalNum;
                $logs[] = $loginfo;

                if (count($logs) > 100) {
                    LeaseStartTerminalLog::insert($logs);
                    $logs = [];
                }
            }
            LeaseStartTerminalLog::insert($logs);
            systemLog("生成启动终端统计日志成功");
        } catch (\Exception $e) {
            \Log::error("生成启动终端统计日志失败: {$e->getMessage()}");
            systemLog("生成启动终端统计日志失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成启动终端统计日志失败：{$exception->getMessage()}");
        systemLog("生成启动终端统计日志失败: 详情请见laravel-log");
    }
}
