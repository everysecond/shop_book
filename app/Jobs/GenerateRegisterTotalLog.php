<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Manage\Models\Report\LeaseRegisterLog;
use Modules\Manage\Models\Report\LeaseUser;

class GenerateRegisterTotalLog implements ShouldQueue
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
            $allProvinces = allLeaseProvinces();
            $maxDate = LeaseRegisterLog::where("type",LeaseRegisterLog::LOG_TYPE_TWO)->max("date");
            $begin = $end = date("Y-m-d");
            if (!$maxDate) {
                $firstUser = LeaseUser::min("created_at");
                $begin = $firstUser?date("Y-m-d", $firstUser):$begin;
            } else {
                $begin = $maxDate;
                LeaseRegisterLog::whereBetween("date", [$begin, $end])->where("type",LeaseRegisterLog::LOG_TYPE_TWO)->delete();
            }
            //24小时数组
            $registerNumArr = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
            $sql = LeaseUser::selectRaw("count(id) as num,register_at,register_hour,province_id")
                ->groupBy("register_at","register_hour")
                ->orderBy("register_at")
                ->orderBy("register_hour");
            $nationData = clone $sql;
            //全国只按日期分组
            $nationData = $nationData->get()->toArray();
            //按省按日期分组
            $provinceData = $sql->groupBy("province_id")->get()->toArray();
            $logModel = [
                "type"         => 2,
                "province_id"  => 0,
                "register_num_str"    => '',
                "total"    => 0,
                "created_at" => time(),
                "updated_at" => time()
            ];
            $dateArr = getDateRange($begin, $end);
            $registerLogs = [];
            foreach ($dateArr as $date) {
                //记录一条全国当天新用户租赁信息
                $loginfo = $logModel;
                $loginfo["date"] = $date;
                //当天总计
                $totalNum = 0;
                //累计到当天总计
                $sumTotal = 0;
                $nRegisterNumArr = $registerNumArr;
                foreach ($nationData as $item) {
                    if ($item["register_at"] == $date) {
                        $totalNum += $item["num"];
                        $nRegisterNumArr[$item["register_hour"]] = $totalNum;
                        foreach ($nRegisterNumArr as $hour=>&$num) {
                            if($hour > $item["register_hour"]){
                                $num = $totalNum;
                            }
                        }
                    }
                    if (strtotime($item["register_at"]) <= strtotime($date)) {
                        $sumTotal += $item["num"];
                    }
                }
                $loginfo["total"] = $sumTotal;
                $loginfo["register_num_str"] = implode(",",$nRegisterNumArr);
                $registerLogs[] = $loginfo;

                //按每个省记录一条当天新用户租赁信息
                foreach ($allProvinces as $province_id => $province_name) {
                    $loginfo = $logModel;
                    $loginfo["date"] = $date;
                    $loginfo["province_id"] = $province_id;
                    //当天总计
                    $totalNum = 0;
                    //累计到当天总计
                    $sumTotal = 0;
                    $pRegisterNumArr = $registerNumArr;
                    foreach ($provinceData as $datum) {
                        if($datum["province_id"] == $province_id){
                            if ($datum["register_at"] == $date) {
                                $totalNum += $datum["num"];
                                $pRegisterNumArr[$datum["register_hour"]] = $totalNum;
                                foreach ($pRegisterNumArr as $hour=>&$num) {
                                    if($hour > $datum["register_hour"]){
                                        $num = $totalNum;
                                    }
                                }
                            }
                            if (strtotime($datum["register_at"]) <= strtotime($date)) {
                                $sumTotal += $datum["num"];
                            }
                        }
                    }
                    $loginfo["total"] = $sumTotal;
                    $loginfo["register_num_str"] = implode(",",$pRegisterNumArr);
                    $registerLogs[] = $loginfo;
                }
                if (count($registerLogs) > 100) {
                    LeaseRegisterLog::insert($registerLogs);
                    $registerLogs = [];
                }
            }
            LeaseRegisterLog::insert($registerLogs);
            systemLog("生成注册统计日志成功");
        } catch (\Exception $e) {
            \Log::error("生成注册统计日志失败: {$e->getMessage()}");
            systemLog("生成注册统计日志失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成注册统计日志失败：{$exception->getMessage()}");
        systemLog("生成注册统计日志失败: 详情请见laravel-log");
    }
}
