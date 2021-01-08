<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlFlow;
use Modules\Manage\Models\Report\LeaseStartLog;

class GenerateStartLog implements ShouldQueue
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
            //24小时数组
            $startNumArr = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $data = BlFlow::selectRaw("COUNT(id) as num,DATE_FORMAT(created_at, '%H') as hour,"
                ."DATE_FORMAT(created_at, '%Y-%m-%d') as date,DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as time")
                ->whereIn("app_type",[BlFlow::APP_TYPE_ONE,BlFlow::APP_TYPE_TWO])
                ->where("page_url","slide/bootstrap")
                ->groupBy("time");
            $logModel = [
                "province_id"   => 0,
                "start_num_str" => '',
                "total"         => 0,
                "created_at"    => time(),
                "updated_at"    => time()
            ];
            $flowData = [];
            $maxDate = LeaseStartLog::query()
                ->whereIn("type",[LeaseStartLog::LOG_TYPE_ONE,LeaseStartLog::LOG_TYPE_TWO])
                ->max("date");
            $begin = $end = date("Y-m-d");
            if (!$maxDate) {
                $flowData = $data->get()->toArray();
                $begin = isset($flowData[0]) ? $flowData[0]["date"] : $begin;
            } else {
                $begin = $maxDate;
                LeaseStartLog::whereBetween("date", [$begin, $end])
                    ->whereIn("type",[LeaseStartLog::LOG_TYPE_ONE,LeaseStartLog::LOG_TYPE_TWO])
                    ->delete();
                $flowData = $data->where("created_at", ">", $begin)->get()->toArray();
            }
            $dateArr = getDateRange($begin, $end);
            $startLogs = [];
            foreach ($dateArr as $date) {
                //记录一条全国当天新用户租赁信息
                $loginfo = $logModel;
                $loginfo["date"] = $date;
                $totalNum = 0;
                $todayStartNumArr = $startNumArr;
                $totalStartNumArr = $startNumArr;
                foreach ($flowData as $item) {
                    if ($item["date"] == $date) {
                        $totalNum += $item["num"];
                        $todayStartNumArr[$item["hour"]*1] = $item["num"];
                        $totalStartNumArr[$item["hour"]*1] = $totalNum;
                        foreach ($totalStartNumArr as $hour => &$num) {
                            if ($hour > $item["hour"]) {
                                $num = $totalNum;
                            }
                        }
                    }
                }
                $loginfo["total"] = $totalNum;
                $totalLogInfo = $loginfo;
                $loginfo["type"] = LeaseStartLog::LOG_TYPE_ONE;
                $loginfo["start_num_str"] = implode(",", $todayStartNumArr);
                $startLogs[] = $loginfo;

                $totalLogInfo["type"] = LeaseStartLog::LOG_TYPE_TWO;
                $totalLogInfo["start_num_str"] = implode(",", $totalStartNumArr);
                $startLogs[] = $totalLogInfo;
                if (count($startLogs) > 100) {
                    LeaseStartLog::insert($startLogs);
                    $startLogs = [];
                }
            }
            LeaseStartLog::insert($startLogs);
            systemLog("生成启动统计日志成功");
        } catch (\Exception $e) {
            \Log::error("生成启动统计日志失败: {$e->getMessage()}");
            systemLog("生成启动统计日志失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成启动统计日志失败：{$exception->getMessage()}");
        systemLog("生成启动统计日志失败: 详情请见laravel-log");
    }
}
