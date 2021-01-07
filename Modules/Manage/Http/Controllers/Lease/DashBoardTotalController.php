<?php

namespace Modules\Manage\Http\Controllers\Lease;

use Doctrine\DBAL\Schema\AbstractAsset;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Lease\Models\BlAppDown;
use Modules\Lease\Models\BlFlow;
use Modules\Lease\Models\BlLeaseExchange;
use Modules\Lease\Models\BlLeaseLost;
use Modules\Lease\Models\BlLeaseRetire;
use Modules\Lease\Models\BlUserInsurance;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseContractDateInfo;
use Modules\Manage\Models\Report\LeasePayment;
use Modules\Manage\Models\Report\LeaseUser;

class DashBoardTotalController extends Controller
{
    //整体趋势view
    public function totalIndex()
    {
        $provinces = allUserProvinces(0);
        return view("manage::lease.report.dashboard.dashboard_total", compact("provinces"));
    }

    //整体趋势-基本指标数据
    public function baseTotalData(Request $request)
    {
        try {
            $agentId = $request->get("agentId");
            $returnData = [
                "register" => $this->getRegisterNumAndRate($agentId),
                "down"     => $this->getDownNumAndRate(),
                "login"    => $this->getLoginNumAndRate($agentId)
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //累计到当日及累积到前一日注册数量及增量比例
    public function getRegisterNumAndRate($agentId)
    {
        $day = date("Y-m-d");
        $registerData = LeaseUser::selectRaw("count(id) as num,register_at");
        if ($agentId) {
            $registerData->where("province_id", $agentId);
        }
        $todayTotal = clone $registerData;
        $todayTotal = $todayTotal->first();
        $beforeTotal = $registerData->where("register_at", "<", $day)->first();
        $default = ["num" => 0, "rate" => "0"];
        if ($todayTotal) {
            $default["num"] = $todayTotal->num;
            if ($beforeTotal && $beforeTotal->num) {
                $default["rate"] = (round(($todayTotal->num - $beforeTotal->num) / $beforeTotal->num * 10000) / 100);
            }
        }
        return $default;
    }

    //累积到当日及累积到前一日下载数量及增量比例
    public function getDownNumAndRate()
    {
        $day = date("Ymd");
        $data = BlAppDown::selectRaw("count(id) as num,day")->where('app_type', BlAppDown::APP_TYPE_ONE);
        $todayTotal = clone $data;
        $todayTotal = $todayTotal->first();
        $beforeTotal = $data->where("day", "<", $day)->first();
        $default = ["num" => 0, "rate" => "0"];
        if ($todayTotal) {
            $default["num"] = $todayTotal->num;
            if ($beforeTotal && $beforeTotal->num) {
                $default["rate"] = (round(($todayTotal->num - $beforeTotal->num) / $beforeTotal->num * 10000) / 100);
            }
        }
        return $default;
    }

    //最近七日及前七日登录用户数及环比增量比例
    public function getLoginNumAndRate($agentId)
    {
        $sixDayBefore = date("Ymd", strtotime("-6 days"));
        $thirteenDayBefore = date("Ymd", strtotime("-13 days"));
        $data = BlFlow::selectRaw("COUNT(DISTINCT bl_flows.user_id) as num ")
            ->leftjoin("bl_users", "bl_flows.user_id", "=", "bl_users.id")
            ->where("bl_flows.user_id", ">", 0)
            ->where("bl_flows.page_url", 'slide/middle')
            ->whereIn("bl_flows.app_type", [BlFlow::APP_TYPE_ONE, BlFlow::APP_TYPE_TWO]);
        if ($agentId) {
            $data->where("bl_users.province_id", $agentId);
        }
        $currentSevenDayData = clone $data;
        $currentSevenDayData = $currentSevenDayData->where("day", ">=", $sixDayBefore)->first();
        $prevSevenDayData = $data->where("day", ">=", $thirteenDayBefore)->where("day", "<", $sixDayBefore)->first();
        $default = ["num" => 0, "rate" => "0"];
        if ($currentSevenDayData) {
            $default["num"] = $currentSevenDayData->num;
            if ($prevSevenDayData && $prevSevenDayData->num) {
                $default["rate"] = (round(($currentSevenDayData->num - $prevSevenDayData->num)
                        / $prevSevenDayData->num * 10000) / 100);
            }
        }
        return $default;
    }

    //各时点基本指标折线图数据
    public function totalChartData(Request $request)
    {
        try {
            $type = $request->get("type", "register");
            $defaultDay = $request->get("date", date("Y-m-d"));
            $agentId = $request->get("agentId");
            $defaultNumArr = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            if ($type == "register") {
                $returnData = $this->getRegisterData($agentId);
            } elseif ($type == "down") {
                $returnData = $this->getDownData();
            } elseif ($type == "login") {
                $returnData = $this->getLoginData($agentId);
            }
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //当日各时点注册量
    public function getRegisterData($agentId)
    {
        $registerData = LeaseUser::selectRaw("count(id) as num,date_format(register_at,'%Y-%m') as register_month");
        if ($agentId) {
            $registerData->where("province_id", $agentId);
        }
        $registerData = $registerData->groupBy("register_month")->orderBy("register_month")->get();
        $series = [
            "name"       => "当月累计注册量",
            "type"       => 'line',
            "stack"      => "当月累计注册量",
            "symbolSize" => 6,
            "symbol"     => 'circle',
            "data"       => []
        ];
        $monthArr = [];
        foreach ($registerData as $item) {
            $series["data"][] = $item->num;
            $monthArr[] = $item->register_month;
        }
        return [
            "days"    => ["累计注册量"],
            "hourArr" => $monthArr,
            "series"  => [$series]
        ];
    }

    //各月份下载量
    public function getDownData()
    {
        $data = BlAppDown::selectRaw("created_at,count(id) as num");
        $data = $data->groupBy("month")->orderBy("month")->get();
        $series = [
            "name"       => "当月累计下载量",
            "type"       => 'line',
            "stack"      => "当月累计下载量",
            "symbolSize" => 6,
            "symbol"     => 'circle',
            "data"       => []
        ];
        $monthArr = [];
        foreach ($data as $item) {
            $series["data"][] = $item->num;
            $monthArr[] = date("Y-m", strtotime($item->created_at));
        }
        return [
            "days"    => ["累计下载量"],
            "hourArr" => $monthArr,
            "series"  => [$series]
        ];
    }

    //最近七日用户登录数
    public function getLoginData($agentId)
    {
        $day = date("Ymd", strtotime("-6 days"));
        $data = BlFlow::selectRaw("bl_flows.day,COUNT(DISTINCT bl_flows.user_id) as num ")
            ->leftjoin("bl_users", "bl_flows.user_id", "=", "bl_users.id")
            ->where("bl_flows.user_id", ">", 0)
            ->where("bl_flows.page_url", 'slide/middle')
            ->whereIn("bl_flows.app_type", [BlFlow::APP_TYPE_ONE, BlFlow::APP_TYPE_TWO]);
        if ($agentId) {
            $data->where("bl_users.province_id", $agentId);
        }
        //老用户
        $old = clone $data;
        $new = $data->where("bl_flows.day", ">=", $day)->groupBy("bl_flows.day")->orderBy("bl_flows.day")->get();
        $serie = [
            "name"       => "当天登录用户数",
            "type"       => 'line',
            "stack"      => "当天登录用户数",
            "symbolSize" => 6,
            "symbol"     => 'circle',
            "data"       => []
        ];
        $dayArr = [];
        foreach ($new as $item) {
            $serie["data"][] = $item->num;
            $dayArr[] = date("m-d", strtotime($item->day));
        }

        return [
            "days"    => ["近期日登录用户数"],
            "hourArr" => $dayArr,
            "series"  => [$serie]
        ];
    }

    //今日指标-租赁指标
    public function leaseData(Request $request)
    {
        try {
            $agentId = $request->get("agentId");
            $leaseData = $this->getLeaseNum($agentId);
            $returnData = [
                "lease_month" => $this->getLeaseMonth($agentId),
            ];
            return result("", 1, array_merge($leaseData, $returnData));
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //续租指标
    public function renewalData(Request $request)
    {
        try {
            $agentId = $request->get("agentId");
            $leaseData = $this->getRenewalNum($agentId);
            $returnData = [
                "lease_month" => $this->getRenewaMonth($agentId),
            ];
            return result("", 1, array_merge($leaseData, $returnData));
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //续租
    public function getRenewalNum($agentId)
    {
        $data = LeasePayment::selectRaw("count(*) as num,sum(rental) as amount")
            ->where('status', 1)->whereIn('type', [2, 3]);
        if ($agentId) {
            $data->where("province_id", $agentId);
        }
        $data = $data->first();
        $default = [
            "lease_num"    => ["num" => 0],
            "lease_amount" => ["num" => 0]
        ];
        if ($data) {
            $default["lease_num"]["num"] = $data->num;
            $default["lease_amount"]["num"] = $data->amount;
        }
        return $default;
    }

    //退租指标
    public function rebatelData(Request $request)
    {
        try {
            $agentId = $request->get("agentId");
            $leaseData = $this->getRebatelNum($agentId);

            return result("", 1, $leaseData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //退租
    public function getRebatelNum($agentId)
    {
        $data = app(BlLeaseRetire::class)
            ->selectRaw("count(*) as num,sum(amount) as amount")
            ->leftJoin('bl_lease_services', 'bl_lease_retires.id', '=', 'bl_lease_services.serviceable_id')
            ->leftJoin('bl_lease_contracts', 'bl_lease_contracts.id', '=', 'bl_lease_services.contract_id')
            ->where('bl_lease_services.serviceable_type', '=', 'App\Models\LeaseRetireModel')
            ->where('bl_lease_retires.status', 3);
        if ($agentId) {
            $data->where("bl_lease_contracts.province_id", $agentId);
        }
        $data = $data->first();

        $default = [
            "lease_num"    => ["num" => 0],
            "lease_amount" => ["num" => 0]
        ];
        if ($data) {
            $default["lease_num"]["num"] = $data->num;
            $default["lease_amount"]["num"] = $data->amount;
        }
        return $default;
    }

    //换租指标
    public function changeData(Request $request)
    {
        try {
            $agentId = $request->get("agentId");
            $leaseData = $this->getChangeNum($agentId);

            return result("", 1, $leaseData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //换租
    public function getChangeNum($agentId)
    {
        $data = app(BlLeaseExchange::class)
            ->selectRaw("count(*) as num")
            ->leftJoin('bl_lease_services', 'bl_lease_exchanges.id', '=', 'bl_lease_services.serviceable_id')
            ->leftJoin('bl_lease_contracts', 'bl_lease_contracts.id', '=', 'bl_lease_services.contract_id')
            ->where('bl_lease_services.serviceable_type', '=', 'App\Models\LeaseExchangeModel')
            ->where('bl_lease_exchanges.status', '=', 4);
        if ($agentId) {
            $data->where("bl_lease_contracts.province_id", $agentId);
        }
        $data = $data->first();

        $default = [
            "lease_num" => ["num" => 0],
        ];
        if ($data) {
            $default["lease_num"]["num"] = $data->num;
        }
        return $default;
    }

    //投保指标
    public function insuranceData(Request $request)
    {
        try {
            $agentId = $request->get("agentId");
            $leaseData = $this->getInsuranceNum($agentId);

            return result("", 1, $leaseData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //投保
    public function getInsuranceNum($agentId)
    {
        $data = app(BlUserInsurance ::class)
            ->selectRaw("count(*) as num")
            ->where("status", "=", 20);
        $report_lost_num = app(BlLeaseLost::class)
            ->selectRaw("count(*) as lost_num");

        if ($agentId) {
            $data->where("province_id", $agentId);
            $report_lost_num->where("province_id", $agentId);
        }
        $data = $data->first();
        $report_lost_num = $report_lost_num->first();

        $default = [
            "lease_num"    => ["num" => 0],
            "lease_amount" => ["num" => 0]
        ];
        if ($data || $report_lost_num) {
            $default["lease_num"]["num"] = $data->num;
            $default["lease_amount"]["num"] = $report_lost_num->lost_num;
        }
        return $default;
    }

    //续租周期
    public function getRenewaMonth($agentId)
    {
        $data = app(LeasePayment::class)
            ->selectRaw("contract_id")
            ->whereIn("type", [LeaseContract::PAYMENT_TYPE_TWO, LeaseContract::PAYMENT_TYPE_THREE])
            ->where("status", LeaseContract::PAYMENT_STATUS_ONE);
        if ($agentId) {
            $data->where("province_id", $agentId);
        }
        $data = $data->with("contract")->get()->toArray();

        $default = ["num" => 0];
        $total = 0;
        foreach ($data as $datum) {
            if ($datum['contract']["lease_unit"] == "year") {
                $total += $datum['contract']["lease_term"] * 12;
            } elseif ($datum['contract']["lease_unit"] == "month") {
                $total += $datum['contract']["lease_term"];
            }
        }
        $default["num"] = $total;
        return $default;
    }

    //当日及前一日租赁数量金额及增量比例
    public function getLeaseNum($agentId)
    {
        $data = LeaseContractDateInfo::selectRaw("sum(total_num) as num,sum(total_rental) as amount,sum(total_deposit) as lease_deposit");
        if ($agentId) {
            $data->where("province_id", $agentId);
        } else {
            $data->where("province_id", 0);
        }
        $data = $data->groupBy("date")->orderBy("date", "desc")->first();
        $default = [
            "lease_num"     => ["num" => 0],
            "lease_amount"  => ["num" => 0],
            "lease_deposit" => ["num" => 0]
        ];
        if ($data) {
            $default["lease_num"]["num"] = $data->num;
            $default["lease_amount"]["num"] = $data->amount;
            $default["lease_deposit"]["num"] = $data->lease_deposit;
        }
        return $default;
    }

    //当日及前一日租赁月数及增量比例
    public function getLeaseMonth($agentId)
    {
        $data = LeaseContract::selectRaw("lease_term,lease_unit")
            ->where("payment_type", LeaseContract::PAYMENT_TYPE_ONE)
            ->where("payment_status", LeaseContract::PAYMENT_STATUS_ONE);
        if ($agentId) {
            $data->where("province_id", $agentId);
        }
        $data = $data->get()->toArray();
        $default = ["num" => 0];
        $total = 0;
        foreach ($data as $datum) {
            if ($datum["lease_unit"] == "year") {
                $total += $datum["lease_term"] * 12;
            } elseif ($datum["lease_unit"] == "month") {
                $total += $datum["lease_term"];
            }
        }
        $default["num"] = $total;
        return $default;
    }
}