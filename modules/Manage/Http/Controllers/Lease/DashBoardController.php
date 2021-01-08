<?php

namespace Modules\Manage\Http\Controllers\Lease;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Lease\Models\BlAppDown;
use Modules\Lease\Models\BlFlow;
use Modules\Manage\Models\LeaseInsurance;
use Modules\Manage\Models\LeaseProcessHour;
use Modules\Manage\Models\LeaseRenewal;
use Modules\Manage\Models\LeaseRenewalReport;
use Modules\Manage\Models\LeaseRentChange;
use Modules\Manage\Models\LeaseRentRebate;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseContractDateInfo;
use Modules\Manage\Models\Report\LeasePayment;
use Modules\Manage\Models\Report\LeaseStartLog;
use Modules\Manage\Models\Report\LeaseUser;

class DashBoardController extends Controller
{
    protected $hourArr = ["0:00", "1:00", "2:00", "3:00", "4:00", "5:00", "6:00", "7:00", "8:00",
        "9:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00",
        "19:00", "20:00", "21:00", "22:00", "23:00"];

    //今日指标view
    public function todayIndex()
    {
        $provinces = allUserProvinces(0);
        return view("manage::lease.report.dashboard.dashboard_today", compact("provinces"));
    }

    //今日指标-基本指标数据
    public function baseData(Request $request)
    {
        try {
            //默认获取当天数据
            $defaultDay = date("Y-m-d");
            if ($day = $request->get("date")) {
                $defaultDay = $day;
            }
            $agentId = $request->get("agentId");
            $defaultDayArr = [
                date("Y-m-d", strtotime($defaultDay) - 86400),
                $defaultDay
            ];
            $returnData = [
                "register" => $this->getRegisterNumAndRate($defaultDayArr, $agentId),
                "down"     => $this->getDownNumAndRate($defaultDayArr),
                "start"    => $this->getStartNumAndRate($defaultDayArr),
                "login"    => $this->getLoginNumAndRate($defaultDayArr, $agentId)
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //当日及前一日注册数量及增量比例
    public function getRegisterNumAndRate($dayArr, $agentId)
    {
        $registerData = LeaseUser::selectRaw("count(id) as num,register_at")
            ->whereIn("register_at", $dayArr);
        if ($agentId) {
            $registerData->where("province_id", $agentId);
        }
        $data = $registerData->groupBy("register_at")->orderBy("register_at")->get()->toArray();
        $default = ["num" => 0, "rate" => "0"];
        if (count($data) == 2) {
            $default["num"] = $data[1]["num"];
            $default["rate"] = round(($data[1]["num"] - $data[0]["num"]) / $data[0]["num"] * 100);
        } elseif (count($data) == 1) {
            if ($data[0]["register_at"] == $dayArr[1]) {
                $default["num"] = $data[0]["num"];
                $default["rate"] = "-";
            }
        }
        return $default;
    }

    //当日及前一日下载数量及增量比例
    public function getDownNumAndRate($dayArr)
    {
        $dayArr = [
            date("Ymd", strtotime($dayArr[0])),
            date("Ymd", strtotime($dayArr[1]))
        ];
        $registerData = BlAppDown::selectRaw("count(id) as num,day")
            ->where('app_type', BlAppDown::APP_TYPE_ONE)
            ->whereIn("day", $dayArr);
        $data = $registerData->groupBy("day")->orderBy("day")->get()->toArray();
        $default = ["num" => 0, "rate" => "0"];
        if (count($data) == 2) {
            $default["num"] = $data[1]["num"];
            $default["rate"] = round(($data[1]["num"] - $data[0]["num"]) / $data[0]["num"] * 100);
        } elseif (count($data) == 1) {
            if ($data[0]["day"] == $dayArr[1]) {
                $default["num"] = $data[0]["num"];
                $default["rate"] = "-";
            }
        }
        return $default;
    }

    //当日及前一日启动次数及增量比例
    public function getStartNumAndRate($dayArr)
    {
        $registerData = LeaseStartLog::selectRaw("total as num,date")
            ->where("type", LeaseStartLog::LOG_TYPE_ONE)
            ->whereIn("date", $dayArr);
        $data = $registerData->groupBy("date")->orderBy("date")->get()->toArray();
        $default = ["num" => 0, "rate" => "0"];
        if (count($data) == 2) {
            $default["num"] = $data[1]["num"];
            $default["rate"] = round(($data[1]["num"] - $data[0]["num"]) / $data[0]["num"] * 100);
        } elseif (count($data) == 1) {
            if ($data[0]["date"] == $dayArr[1]) {
                $default["num"] = $data[0]["num"];
                $default["rate"] = "-";
            }
        }
        return $default;
    }

    //当日及前一日登录用户数及增量比例
    public function getLoginNumAndRate($dayArr, $agentId)
    {
        $dayArr = [
            date("Ymd", strtotime($dayArr[0])),
            date("Ymd", strtotime($dayArr[1]))
        ];
        $registerData = BlFlow::selectRaw(" COUNT(DISTINCT bl_flows.user_id) as num,bl_flows.day ")
            ->leftjoin("bl_users", "bl_flows.user_id", "=", "bl_users.id")
            ->where("bl_flows.user_id", ">", 0)
            ->where("bl_flows.page_url", 'slide/middle')
            ->whereIn("bl_flows.day", $dayArr)
            ->whereIn("app_type", [BlFlow::APP_TYPE_ONE, BlFlow::APP_TYPE_TWO]);
        if ($agentId) {
            $registerData->where("bl_users.province_id", $agentId);
        }
        $data = $registerData->groupBy("bl_flows.day")->orderBy("bl_flows.day")->get()->toArray();
        $default = ["num" => 0, "rate" => "0"];
        if (count($data) == 2) {
            $default["num"] = $data[1]["num"];
            $default["rate"] = round(($data[1]["num"] - $data[0]["num"]) / $data[0]["num"] * 100);
        } elseif (count($data) == 1) {
            if ($data[0]["day"] == $dayArr[1]) {
                $default["num"] = $data[0]["num"];
                $default["rate"] = "-";
            }
        }
        return $default;
    }


    //各时点基本指标折线图数据
    public function baseChartData(Request $request)
    {
        try {
            $type = $request->get("type", "register");
            $defaultDay = $request->get("date", date("Y-m-d")) ?? date("Y-m-d");
            $agentId = $request->get("agentId");
            $defaultNumArr = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            if ($type == "register") {
                $returnData = $this->getRegisterData($defaultDay, $agentId, $defaultNumArr);
            } elseif ($type == "down") {
                $returnData = $this->getDownData($defaultDay, $defaultNumArr);
            } elseif ($type == "start") {
                $returnData = $this->getStartData($defaultDay, $defaultNumArr);
            } elseif ($type == "login") {
                $returnData = $this->getLoginData($defaultDay, $agentId, $defaultNumArr);
            }
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //当日各时点注册量
    public function getRegisterData($day, $agentId, $defaultNumArr)
    {
        $registerData = LeaseUser::selectRaw("count(id) as num,register_at,register_hour")->where("register_at", $day);
        if ($agentId) {
            $registerData->where("province_id", $agentId);
        }
        $registerData = $registerData->groupBy("register_hour")->orderBy("register_hour")->get();
        $series = [
            "name"       => $day,
            "type"       => 'line',
            "stack"      => $day,
            "symbolSize" => 6,
            "symbol"     => 'circle',
            "data"       => $defaultNumArr
        ];
        foreach ($registerData as $item) {
            $series["data"][$item->register_hour * 1] = $item->num;
        }
        return [
            "days"    => [$day],
            "hourArr" => $this->hourArr,
            "series"  => [$series]
        ];
    }

    //当日各时点下载量
    public function getDownData($day, $defaultNumArr)
    {
        $oldDay = $day;
        $day = date("Ymd", strtotime($day));
        $data = BlAppDown::selectRaw("FROM_UNIXTIME(created_at,'%H') as hour,count(id) as num,day")
            ->where('app_type', BlAppDown::APP_TYPE_ONE)
            ->where("day", $day);
        $data = $data->groupBy("hour")->orderBy("hour")->get();
        $series = [
            "name"       => $oldDay,
            "type"       => 'line',
            "stack"      => $oldDay,
            "symbolSize" => 6,
            "symbol"     => 'circle',
            "data"       => $defaultNumArr
        ];
        foreach ($data as $item) {
            $series["data"][$item->hour * 1] = $item->num;
        }
        return [
            "days"    => [$oldDay],
            "hourArr" => $this->hourArr,
            "series"  => [$series]
        ];
    }

    //当日各时点启动数
    public function getStartData($day, $defaultNumArr)
    {
        $data = LeaseStartLog::query()->select('date', 'start_num_str')
            ->where("type", LeaseStartLog::LOG_TYPE_ONE)
            ->where("date", $day)
            ->first();
        $series = [
            "name"       => $day,
            "type"       => 'line',
            "stack"      => $day,
            "symbolSize" => 6,
            "symbol"     => 'circle',
            "data"       => explode(',', $data->start_num_str)
        ];
        return [
            "days"    => [$day],
            "hourArr" => $this->hourArr,
            "series"  => [$series]
        ];
    }

    //当日各时点新老用户登录数
    public function getLoginData($day, $agentId, $defaultNumArr)
    {
        $day = date("Ymd", strtotime($day));
        $data = BlFlow::selectRaw("date_format(bl_flows.created_at,'%H') as created_hour,COUNT(DISTINCT bl_flows.user_id) as num ")
            ->leftjoin("bl_users", "bl_flows.user_id", "=", "bl_users.id")
            ->where("bl_flows.user_id", ">", 0)
            ->where("bl_flows.page_url", 'slide/middle')
            ->where("bl_flows.day", $day)
            ->whereIn("bl_flows.app_type", [BlFlow::APP_TYPE_ONE, BlFlow::APP_TYPE_TWO]);
        if ($agentId) {
            $data->where("bl_users.province_id", $agentId);
        }
        //老用户
        $old = clone $data;
        $new = $data->where("bl_users.created_at", ">=", $day)->groupBy("created_hour")->orderBy("created_hour")->get();
        $old = $old->where("bl_users.created_at", "<", $day)->groupBy("created_hour")->orderBy("created_hour")->get();
        $serieModel = [
            "name"       => "",
            "type"       => 'line',
            "stack"      => "",
            "symbolSize" => 6,
            "symbol"     => 'circle',
            "data"       => $defaultNumArr
        ];
        $serieNew = $serieModel;
        $serieNew["name"] = "新用户";
        $serieNew["stack"] = "新用户";
        foreach ($new as $item) {
            $serieNew["data"][$item->created_hour * 1] = $item->num;
        }

        $serieOld = $serieModel;
        $serieOld["name"] = "老用户";
        $serieOld["stack"] = "老用户";
        foreach ($old as $item) {
            $serieOld["data"][$item->created_hour * 1] = $item->num;
        }

        return [
            "days"    => ["新用户", "老用户"],
            "hourArr" => $this->hourArr,
            "series"  => [$serieNew, $serieOld]
        ];
    }


    //今日指标-租赁指标
    public function leaseData(Request $request)
    {
        try {
            //默认获取当天数据
            $defaultDay = date("Y-m-d");
            if ($day = $request->get("date")) {
                $defaultDay = $day;
            }
            $agentId = $request->get("agentId");
            $defaultDayArr = [
                date("Y-m-d", strtotime($defaultDay) - 86400),
                $defaultDay
            ];
            $leaseData = $this->getLeaseNumAndRate($defaultDayArr, $agentId);
            $returnData = [
                "lease_month"     => $this->getLeaseMonthNumAndRate($defaultDayArr, $agentId),
                "expired_today"   => $this->getExpiredTodayNumAndRate($defaultDayArr, $agentId),
                "expired_10"      => $this->getExpiredTenDaysNumAndRate($defaultDayArr, $agentId),
                "expired_10_2_30" => $this->getExpiredTenToThirteenDaysNumAndRate($defaultDayArr, $agentId),
                "expired_30"      => $this->getExpiredThirteenDaysNumAndRate($defaultDayArr, $agentId),
            ];
            return result("", 1, array_merge($leaseData, $returnData));
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //当日及前一日租赁数量金额及增量比例
    public function getLeaseNumAndRate($dayArr, $agentId)
    {
        $registerData = LeaseContractDateInfo::selectRaw("sum(today_num) as num,sum(today_rental) as amount,sum(today_deposit) as deposit,date")
            ->whereIn("date", $dayArr);
        if ($agentId) {
            $registerData->where("province_id", $agentId);
        } else {
            $registerData->where("province_id", 0);
        }
        $data = $registerData->groupBy("date")->orderBy("date")->get()->toArray();
        $default = [
            "lease_num"     => ["num" => 0, "rate" => "0"],
            "lease_amount"  => ["num" => 0, "rate" => "0"],
            "lease_deposit" => ["num" => 0, "rate" => "0"]
        ];
        if (count($data) == 2) {
            $default["lease_num"]["num"] = $data[1]["num"];
            $default["lease_amount"]["num"] = $data[1]["amount"];
            $default["lease_deposit"]["num"] = $data[1]["deposit"];
            if ($data[0]["num"] * 1) {
                $default["lease_num"]["rate"] = round(($data[1]["num"] - $data[0]["num"]) / $data[0]["num"] * 100);
            } else {
                $default["lease_num"]["rate"] = "-";
            }
            if ($data[0]["amount"] * 1) {
                $default["lease_amount"]["rate"] = round(($data[1]["amount"] - $data[0]["amount"]) / $data[0]["amount"] * 100);
            } else {
                $default["lease_amount"]["rate"] = "-";
            }
            if ($data[0]["deposit"] * 1) {
                $default["lease_deposit"]["rate"] = round(($data[1]["deposit"] - $data[0]["deposit"]) / $data[0]["deposit"] * 100);
            } else {
                $default["lease_deposit"]["rate"] = "-";
            }
        } elseif (count($data) == 1) {
            if ($data[0]["date"] == $dayArr[1]) {
                $default["lease_num"]["num"] = $data[0]["num"];
                $default["lease_num"]["rate"] = "-";
                $default["lease_amount"]["num"] = $data[0]["amount"];
                $default["lease_amount"]["rate"] = "-";
                $default["lease_deposit"]["num"] = $data[0]["amount"];
                $default["lease_deposit"]["rate"] = "-";
            }
        }
        return $default;
    }

    //当日及前一日租赁月数及增量比例
    public function getLeaseMonthNumAndRate($dayArr, $agentId)
    {
        $data = LeaseContract::selectRaw("lease_term,lease_unit,created_date")
            ->whereIn("created_date", $dayArr)
            ->where("payment_type", LeaseContract::PAYMENT_TYPE_ONE)
            ->where("payment_status", LeaseContract::PAYMENT_STATUS_ONE);
        if ($agentId) {
            $data->where("province_id", $agentId);
        }
        $data = $data->orderBy("created_date")->get()->toArray();
        $default = ["num" => 0, "rate" => "0"];
        $today = 0;
        $yesterday = 0;
        foreach ($dayArr as $k => $day) {
            foreach ($data as $datum) {
                if ($day == $datum["created_date"]) {
                    //当天
                    if ($k == 1) {
                        if ($datum["lease_unit"] == "year") {
                            $today += $datum["lease_term"] * 12;
                        } elseif ($datum["lease_unit"] == "month") {
                            $today += $datum["lease_term"];
                        }
                    }
                    //前一天
                    if ($k == 0) {
                        if ($datum["lease_unit"] == "year") {
                            $yesterday += $datum["lease_term"] * 12;
                        } elseif ($datum["lease_unit"] == "month") {
                            $yesterday += $datum["lease_term"];
                        }
                    }
                }
            }
        }
        $default["num"] = $today;
        if ($yesterday != 0) {
            $default["rate"] = round(($today - $yesterday) / $yesterday * 100);
        } else {
            $default["rate"] = "-";
        }
        return $default;
    }

    //当日及前一日租赁到期数量及增量比例
    public function getExpiredTodayNumAndRate($dayArr, $agentId)
    {
        $dayArrStr = "'" . implode("','", $dayArr) . "'";
        $data = LeaseContract::selectRaw("count(id) as num,DATE_FORMAT(lease_expired_at,'%Y-%m-%d') as expired_date ")
            ->havingRaw(" expired_date in ($dayArrStr) ")
            ->whereIn("status", [3, 4, 5, 7, 8])
            ->where("payment_type", LeaseContract::PAYMENT_TYPE_ONE)
            ->where("payment_status", LeaseContract::PAYMENT_STATUS_ONE);
        if ($agentId) {
            $data->where("province_id", $agentId);
        }
        $data = $data->groupBy("expired_date")->orderBy("expired_date")->get()->toArray();
        $todayNum = 0;
        $yesterdayNum = 0;
        if (count($data) == 2) {
            $todayNum = $data[1]["num"];
            $yesterdayNum = $data[0]["num"];
        } elseif (count($data) == 1) {
            if ($data[0]["expired_date"] == $dayArr[1]) {
                $todayNum = $data[0]["num"];
            } else {
                $yesterdayNum = $data[0]["num"];
            }
        };
        $todayRenewalNum = $this->getRenewalNum("expire_renewal_num", $agentId, $dayArr[1]);
        $yesterdayRenewalNum = $this->getRenewalNum("expire_renewal_num", $agentId, $dayArr[0]);
        $todayNum -= $todayRenewalNum;
        $yesterdayNum -= $yesterdayRenewalNum;
        $todayNum = $todayNum >= 0 ? $todayNum : 0;
        $yesterdayNum = $yesterdayNum >= 0 ? $yesterdayNum : 0;
        $default = ["num" => $todayNum, "rate" => "0"];
        if ($yesterdayNum > 0) {
            $default["rate"] = round(($todayNum - $yesterdayNum) / $yesterdayNum * 100);
        } else {
            $default["rate"] = "-";
        }
        return $default;
    }

    //当日及前一日租赁已到期0-10天数量及增量比例
    public function getExpiredTenDaysNumAndRate($dayArr, $agentId)
    {
        $todayArr = [
            getAppointDate($dayArr[1], -10),
            getAppointDate($dayArr[1], 1)
        ];

        $yesterdayArr = [
            getAppointDate($dayArr[0], -10),
            getAppointDate($dayArr[0], 1)
        ];

        $data = LeaseContract::selectRaw("count(id) as num")
            ->where("payment_type", LeaseContract::PAYMENT_TYPE_ONE)
            ->where("payment_status", LeaseContract::PAYMENT_STATUS_ONE);

        if ($agentId) {
            $data->where("province_id", $agentId);
        }

        $todayData = clone $data;
        $todayNum = $todayData->whereBetween("lease_expired_at", $todayArr)->first();
        $yesterdayNum = $data->whereBetween("lease_expired_at", $yesterdayArr)->first();
        $todayNum = $todayNum ? $todayNum->num : 0;
        $yesterdayNum = $yesterdayNum ? $yesterdayNum->num : 0;
        $todayRenewalNum = $this->getRenewalNum("overtime_ten_renewal_num", $agentId, $dayArr[1]);
        $yesterdayRenewalNum = $this->getRenewalNum("overtime_ten_renewal_num", $agentId, $dayArr[0]);
        $todayNum -= $todayRenewalNum;
        $yesterdayNum -= $yesterdayRenewalNum;
        $default = ["num" => 0, "rate" => "0"];
        $default["num"] = $todayNum;
        if ($yesterdayNum != 0) {
            $default["rate"] = round(($todayNum - $yesterdayNum) / $yesterdayNum * 100);
        } else if ($todayNum > 0) {
            $default["rate"] = "-";
        }
        return $default;
    }

    //当日及前一日租赁已到期10-30天数量及增量比例
    public function getExpiredTenToThirteenDaysNumAndRate($dayArr, $agentId)
    {
        $todayArr = [
            getAppointDate($dayArr[1], -30),
            getAppointDate($dayArr[1], -10)
        ];

        $yesterdayArr = [
            getAppointDate($dayArr[0], -30),
            getAppointDate($dayArr[0], -10)
        ];

        $data = LeaseContract::selectRaw("count(id) as num")
            ->where("payment_type", LeaseContract::PAYMENT_TYPE_ONE)
            ->where("payment_status", LeaseContract::PAYMENT_STATUS_ONE);

        if ($agentId) {
            $data->where("province_id", $agentId);
        }

        $todayData = clone $data;
        $todayNum = $todayData->whereBetween("lease_expired_at", $todayArr)->first();
        $yesterdayNum = $data->whereBetween("lease_expired_at", $yesterdayArr)->first();
        $todayNum = $todayNum ? $todayNum->num : 0;
        $yesterdayNum = $yesterdayNum ? $yesterdayNum->num : 0;
        $todayRenewalNum = $this->getRenewalNum("overtime_ten_thirty_renewal_num", $agentId, $dayArr[1]);
        $yesterdayRenewalNum = $this->getRenewalNum("overtime_ten_thirty_renewal_num", $agentId, $dayArr[0]);
        $todayNum -= $todayRenewalNum;
        $yesterdayNum -= $yesterdayRenewalNum;
        $default = ["num" => 0, "rate" => "0"];
        $default["num"] = $todayNum;
        if ($yesterdayNum != 0) {
            $default["rate"] = round(($todayNum - $yesterdayNum) / $yesterdayNum * 100);
        } else if ($todayNum > 0) {
            $default["rate"] = "-";
        }
        return $default;
    }

    //当日及前一日租赁已到期30天数量及增量比例
    public function getExpiredThirteenDaysNumAndRate($dayArr, $agentId)
    {
        $sql = LeaseRenewalReport::select("overtime_thirty_no_renewal_future_num")
            ->whereType(1);
        $sql->where("province_id", $agentId);
        $today = clone $sql;
        $today = $today->where("renewal_date", getAppointDate($dayArr[1], -30))->first();
        $yesterday = $sql->where("renewal_date", getAppointDate($dayArr[0], -30))->first();
        $todayNum = $today ? $today->overtime_thirty_no_renewal_future_num : 0;
        $yesterdayNum = $yesterday ? $yesterday->overtime_thirty_no_renewal_future_num : 0;
        $default = ["num" => 0, "rate" => "0"];
        $default["num"] = $todayNum;
        if ($yesterdayNum != 0) {
            $default["rate"] = round(($todayNum - $yesterdayNum) / $yesterdayNum * 100);
        } else if ($todayNum > 0) {
            $default["rate"] = "-";
        }
        return $default;
    }

    //今日指标-续租指标
    public function renewalData(Request $request)
    {
        try {
            //默认获取当天数据
            $defaultDay = date("Y-m-d");
            if ($day = $request->get("date")) {
                $defaultDay = $day;
            }
            $agentId = $request->get("agentId");
            $defaultDayArr = [
                date("Y-m-d", strtotime($defaultDay) - 86400),
                $defaultDay
            ];
            $ye_time = date("Y-m-d", strtotime($defaultDay) - 86400);
            $to_time = date("Y-m-d", strtotime($defaultDay) + 86400);
            //selectRaw
            $today_amount = LeasePayment::selectRaw("sum(rental) as amount")
                ->where('payed_at', '>', $defaultDay)->where('payed_at', '<', $to_time)
                ->whereIn("type", [2, 3])->where('status', '=', 1);

            $yesterday_amount = LeasePayment::selectRaw("sum(rental) as amount")
                ->where('payed_at', '>', $ye_time)->where('payed_at', '<', $defaultDay)
                ->whereIn("type", [2, 3])->where('status', '=', 1);


            $renewalData = LeaseRenewal::select("renewal_date", "renewal_amount", "renewal_num", "advance_renewal", "expire_renewal_num",
                "overtime_ten_renewal_num", "overtime_ten_thirty_renewal_num", "renewal_month_total")
                ->whereIn("renewal_date", $defaultDayArr)
                ->whereType(1);
            if ($agentId) {
                $renewalData->where("province_id", $agentId);
                $yesterday_amount->where("province_id", $agentId);
                $today_amount->where("province_id", $agentId);
            } elseif ($agentId == 0) {
                $renewalData->where("province_id", 0);
            }

            $today_amount = $today_amount->first()->toArray();
            $yesterday_amount = $yesterday_amount->first()->toArray();
            $renewalData = $renewalData->get()->toArray();
            $dataArr = [
                "renewal_num"                     => 0,
                "renewal_amount"                  => 0,
                "advance_renewal"                 => 0,
                "expire_renewal_num"              => 0,
                "overtime_ten_renewal_num"        => 0,
                "overtime_ten_thirty_renewal_num" => 0,
                "renewal_month_total"             => 0,
            ];
            $todayArr = $dataArr;
            $yesterdayArr = $dataArr;
            foreach ($defaultDayArr as $k => $day) {
                foreach ($renewalData as $datum) {
                    if ($k == 0 && $day == $datum["renewal_date"]) {
                        $yesterdayArr["renewal_month_total"] = $datum["renewal_month_total"];
                        $yesterdayArr["renewal_num"] = $datum["renewal_num"];
                        $yesterdayArr["renewal_amount"] = isset($yesterday_amount['amount']) ? $yesterday_amount['amount'] : 0;
                        $yesterdayArr["advance_renewal"] = $datum["advance_renewal"];
                        $yesterdayArr["expire_renewal_num"] = $datum["expire_renewal_num"];
                        $yesterdayArr["overtime_ten_renewal_num"] = $datum["overtime_ten_renewal_num"];
                        $yesterdayArr["overtime_ten_thirty_renewal_num"] = $datum["overtime_ten_thirty_renewal_num"];
                    }
                    if ($k == 1 && $day == $datum["renewal_date"]) {
                        $todayArr["renewal_month_total"] = $datum["renewal_month_total"];
                        $todayArr["renewal_num"] = $datum["renewal_num"];
                        $todayArr["renewal_amount"] = isset($today_amount['amount']) ? $today_amount['amount'] : 0;
                        $todayArr["advance_renewal"] = $datum["advance_renewal"];
                        $todayArr["expire_renewal_num"] = $datum["expire_renewal_num"];
                        $todayArr["overtime_ten_renewal_num"] = $datum["overtime_ten_renewal_num"];
                        $todayArr["overtime_ten_thirty_renewal_num"] = $datum["overtime_ten_thirty_renewal_num"];
                    }
                }
            }
            $returnData = $dataArr;
            foreach ($returnData as $key => &$item) {
                $item = ["num" => $todayArr[$key], "rate" => "0"];
                if ($yesterdayArr[$key] > 0) {
                    $item["rate"] = round(($todayArr[$key] - $yesterdayArr[$key]) / $yesterdayArr[$key] * 100);
                } else {
                    $item["rate"] = "-";
                }
            }
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //今日指标-退租指标
    public function rentData(Request $request)
    {
        try {
            //默认获取当天数据
            $defaultDay = date("Y-m-d");
            if ($day = $request->get("date")) {
                $defaultDay = $day;
            }
            $agentId = $request->get("agentId");
            $defaultDayArr = [
                date("Y-m-d", strtotime($defaultDay) - 86400),
                $defaultDay
            ];
            $rentData = LeaseRentRebate::select("rent_release_date", "rent_release_num", "advance_rent_release", "expire_rent_release_num",
                "overtime_ten_rent_release_num", "overtime_ten_thirty_rent_release_num", "rent_release_amount")
                ->whereIn("rent_release_date", $defaultDayArr)
                ->where("type", 1);
            if ($agentId) {
                $rentData->where("province_id", $agentId);
            } elseif ($agentId == 0) {
                $rentData->where("province_id", 0);
            }
            $rentData = $rentData->get()->toArray();
            $dataArr = [
                "rent_release_num"                     => 0,
                "advance_rent_release"                 => 0,
                "expire_rent_release_num"              => 0,
                "overtime_ten_rent_release_num"        => 0,
                "overtime_ten_thirty_rent_release_num" => 0,
                "rent_release_amount"                  => 0,
            ];
            $todayArr = $dataArr;
            $yesterdayArr = $dataArr;
            foreach ($defaultDayArr as $k => $day) {
                foreach ($rentData as $datum) {
                    if ($k == 0 && $day == $datum["rent_release_date"]) {
                        $yesterdayArr["rent_release_amount"] = $datum["rent_release_amount"];
                        $yesterdayArr["rent_release_num"] = $datum["rent_release_num"];
                        $yesterdayArr["advance_rent_release"] = $datum["advance_rent_release"];
                        $yesterdayArr["expire_rent_release_num"] = $datum["expire_rent_release_num"];
                        $yesterdayArr["overtime_ten_rent_release_num"] = $datum["overtime_ten_rent_release_num"];
                        $yesterdayArr["overtime_ten_thirty_rent_release_num"] = $datum["overtime_ten_thirty_rent_release_num"];
                    }
                    if ($k == 1 && $day == $datum["rent_release_date"]) {
                        $todayArr["rent_release_amount"] = $datum["rent_release_amount"];
                        $todayArr["rent_release_num"] = $datum["rent_release_num"];
                        $todayArr["advance_rent_release"] = $datum["advance_rent_release"];
                        $todayArr["expire_rent_release_num"] = $datum["expire_rent_release_num"];
                        $todayArr["overtime_ten_rent_release_num"] = $datum["overtime_ten_rent_release_num"];
                        $todayArr["overtime_ten_thirty_rent_release_num"] = $datum["overtime_ten_thirty_rent_release_num"];
                    }
                }
            }
            $returnData = $dataArr;
            foreach ($returnData as $key => &$item) {
                $item = ["num" => $todayArr[$key], "rate" => "0"];
                if ($yesterdayArr[$key] > 0) {
                    $item["rate"] = round(($todayArr[$key] - $yesterdayArr[$key]) / $yesterdayArr[$key] * 100);
                } else {
                    $item["rate"] = "-";
                }
            }
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //今日指标-换租指标
    public function rentChangeData(Request $request)
    {
        try {
            //默认获取当天数据
            $defaultDay = date("Y-m-d");
            if ($day = $request->get("date")) {
                $defaultDay = $day;
            }
            $agentId = $request->get("agentId");
            $defaultDayArr = [
                date("Y-m-d", strtotime($defaultDay) - 86400),
                $defaultDay
            ];
            $rentData = LeaseRentChange::select("rent_change_date", "rent_change_num")
                ->whereIn("rent_change_date", $defaultDayArr)
                ->where("type", 1);
            if ($agentId) {
                $rentData->where("province_id", $agentId);
            } elseif ($agentId == 0) {
                $rentData->where("province_id", 0);
            }
            $rentData = $rentData->get()->toArray();
            $dataArr = [
                "rent_change_num" => 0
            ];
            $todayArr = $dataArr;
            $yesterdayArr = $dataArr;
            foreach ($defaultDayArr as $k => $day) {
                foreach ($rentData as $datum) {
                    if ($k == 0 && $day == $datum["rent_change_date"]) {
                        $yesterdayArr["rent_change_num"] = $datum["rent_change_num"];
                    }
                    if ($k == 1 && $day == $datum["rent_change_date"]) {
                        $todayArr["rent_change_num"] = $datum["rent_change_num"];
                    }
                }
            }
            $returnData = $dataArr;
            foreach ($returnData as $key => &$item) {
                $item = ["num" => $todayArr[$key], "rate" => "0"];
                if ($yesterdayArr[$key] > 0) {
                    $item["rate"] = round(($todayArr[$key] - $yesterdayArr[$key]) / $yesterdayArr[$key] * 100);
                } else {
                    $item["rate"] = "-";
                }
            }
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //今日指标-保险指标
    public function insuranceData(Request $request)
    {
        try {
            //默认获取当天数据
            $defaultDay = date("Y-m-d");
            if ($day = $request->get("date")) {
                $defaultDay = $day;
            }
            $agentId = $request->get("agentId");
            $defaultDayArr = [
                date("Y-m-d", strtotime($defaultDay) - 86400),
                $defaultDay
            ];
            $rentData = LeaseInsurance::select("rent_date", "insure_num", "report_loss_num")
                ->whereIn("rent_date", $defaultDayArr)
                ->where("type", 1);
            if ($agentId) {
                $rentData->where("province_id", $agentId);
            } elseif ($agentId == 0) {
                $rentData->where("province_id", 0);
            }
            $rentData = $rentData->get()->toArray();
            $dataArr = [
                "insure_num"      => 0,
                "report_loss_num" => 0
            ];
            $todayArr = $dataArr;
            $yesterdayArr = $dataArr;
            foreach ($defaultDayArr as $k => $day) {
                foreach ($rentData as $datum) {
                    if ($k == 0 && $day == $datum["rent_date"]) {
                        $yesterdayArr["insure_num"] = $datum["insure_num"];
                        $yesterdayArr["report_loss_num"] = $datum["report_loss_num"];
                    }
                    if ($k == 1 && $day == $datum["rent_date"]) {
                        $todayArr["insure_num"] = $datum["insure_num"];
                        $todayArr["report_loss_num"] = $datum["report_loss_num"];
                    }
                }
            }
            $returnData = $dataArr;
            foreach ($returnData as $key => &$item) {
                $item = ["num" => $todayArr[$key], "rate" => "0"];
                if ($yesterdayArr[$key] > 0) {
                    $item["rate"] = round(($todayArr[$key] - $yesterdayArr[$key]) / $yesterdayArr[$key] * 100);
                } else {
                    $item["rate"] = "-";
                }
            }
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    public function getRenewalNum($field, $agentId, $date)
    {
        $data = LeaseRenewal::select($field)
            ->where("type", 1)
            ->where("province_id", $agentId ?? 0)
            ->where("renewal_date", $date)
            ->first();
        return $data ? $data->{$field} : 0;
    }
}