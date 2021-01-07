<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseContractDateInfo;

class GenerateOldUserDailyContractInfo implements ShouldQueue
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
            $maxDate = LeaseContractDateInfo::where("type",LeaseContractDateInfo::TYPE_TWO)->max("date");
            $begin = $end = date("Y-m-d");
            if (!$maxDate) {
                $firstContract = LeaseContract::min("created_at");
                $begin = $firstContract?date("Y-m-d", strtotime($firstContract)):$begin;
            } else {
                $begin = $maxDate;
                LeaseContractDateInfo::whereBetween("date", [$begin, $end])->where("type",LeaseContractDateInfo::TYPE_TWO)->delete();
            }
            $sql = LeaseContract::selectRaw("count(id) as num,sum(rental_all) as rental_all,sum(deposit) as deposit,created_date,province_id")
                ->whereRaw(" user_register_at != created_date ")
                ->where("payment_type",LeaseContract::PAYMENT_TYPE_ONE)
                ->where("payment_status",LeaseContract::PAYMENT_STATUS_ONE)
                ->groupBy("created_date")
                ->orderBy("created_date");
            $nationData = clone $sql;
            //全国只按日期分组
            $nationData = $nationData->get()->toArray();
            //按省按日期分组
            $provinceData = $sql->groupBy("province_id")->get()->toArray();
            $dateinfoModel = [
                "type"         => 2,
                "province_id"  => 0,
                "today_num"    => 0,
                "total_num"    => 0,
                "today_rental" => 0,
                "total_rental" => 0,
                "today_deposit" => 0,
                "total_deposit" => 0,
                "created_at" => time(),
                "updated_at" => time()
            ];

            $dateArr = getDateRange($begin, $end);
            $dateInfos = [];
            foreach ($dateArr as $date) {
                //记录一条全国当天新用户租赁信息
                $dateinfo = $dateinfoModel;
                $dateinfo["date"] = $date;
                $totalNum = 0;
                $totalRental = 0;
                $totalDeposit = 0;
                foreach ($nationData as $contract) {
                    if (strtotime($contract["created_date"]) < strtotime($date)) {
                        $totalNum = $totalNum + $contract["num"];
                        $totalRental = $totalRental + $contract["rental_all"];
                        $totalDeposit = $totalDeposit + $contract["deposit"];
                    } elseif ($contract["created_date"] == $date) {
                        $dateinfo["today_num"] = $contract["num"];
                        $dateinfo["today_rental"] = $contract["rental_all"];
                        $dateinfo["today_deposit"] = $contract["deposit"];
                        $totalNum = $totalNum + $contract["num"];
                        $totalRental = $totalRental + $contract["rental_all"];
                        $totalDeposit = $totalDeposit + $contract["deposit"];
                    } else {
                        break;
                    }
                }
                $dateinfo["total_num"] = $totalNum;
                $dateinfo["total_rental"] = $totalRental;
                $dateinfo["total_deposit"] = $totalDeposit;
                $dateInfos[] = $dateinfo;

                //按每个省记录一条当天新用户租赁信息
                foreach ($allProvinces as $province_id => $province_name) {
                    $dateinfo = $dateinfoModel;
                    $dateinfo["date"] = $date;
                    $dateinfo["province_id"] = $province_id;
                    $totalNum = 0;
                    $totalRental = 0;
                    $totalDeposit = 0;
                    foreach ($provinceData as $datum) {
                        //按省份
                        if ($datum["province_id"] == $province_id) {
                            if (strtotime($datum["created_date"]) < strtotime($date)) {
                                $totalNum = $totalNum + $datum["num"];
                                $totalRental = $totalRental + $datum["rental_all"];
                                $totalDeposit = $totalDeposit + $datum["deposit"];
                            } elseif ($datum["created_date"] == $date) {
                                $dateinfo["today_num"] = $datum["num"];
                                $dateinfo["today_rental"] = $datum["rental_all"];
                                $dateinfo["today_deposit"] = $datum["deposit"];
                                $totalNum = $totalNum + $datum["num"];
                                $totalRental = $totalRental + $datum["rental_all"];
                                $totalDeposit = $totalDeposit + $datum["deposit"];
                            } else {
                                break;
                            }
                        }
                    }
                    $dateinfo["total_num"] = $totalNum;
                    $dateinfo["total_rental"] = $totalRental;
                    $dateinfo["total_deposit"] = $totalDeposit;
                    $dateInfos[] = $dateinfo;
                }
                if (count($dateInfos) > 100) {
                    LeaseContractDateInfo::insert($dateInfos);
                    $dateInfos = [];
                }
            }
            LeaseContractDateInfo::insert($dateInfos);
            systemLog("生成老用户每日租赁合约统计信息成功");
        } catch (\Exception $e) {
            \Log::error("生成老用户每日租赁合约统计信息失败: {$e->getMessage()}");
            systemLog("生成老用户每日租赁合约统计信息失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成老用户每日租赁合约统计信息失败：{$exception->getMessage()}");
        systemLog("生成老用户每日租赁合约统计信息失败: 详情请见laravel-log");
    }
}
