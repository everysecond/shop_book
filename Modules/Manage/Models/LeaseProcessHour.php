<?php

namespace Modules\Manage\Models;
use Illuminate\Support\Facades\DB;

class LeaseProcessHour extends Model
{



    public function activeTrend($request, &$defaultDay)
    {
        $days = $request->days;
        $dateRange = $request->dateRange;
        if ($days && $days != -1) {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-$days day")),
                "end"   => date("Y-m-d")
            ];
        } else if ($days && $days == -1 && $dateRange) {
            $days = explode(" - ", $dateRange);
            $defaultDay = [
                "begin" => $days[0],
                "end"   => $days[1]
            ];
        }

        $data = self::selectRaw("sum(login_num) as login_num,sum(index_num) as index_num,sum(scan_num) as scan_num,sum(detail_num) as detail_num,sum(period_num) as period_num,sum(deduction_num) as deduction_num,sum(submit_lease_num) as submit_lease_num ,sum(business_num) as business_num,sum(topay_num) as topay_num,sum(dopay_num) as dopay_num,sum(pay_num) as pay_num,process_date");
        if ($agentId = $request->agentId) {
            $data->where("province_id", $agentId);
        }
        return $data->whereBetween("process_date", $defaultDay)->groupBy("process_date")
            ->orderBy("process_date")->get()->toArray();
    }


    //活跃事件统计
    public function activeData($request, $defaultDay)
    {
        $days = $request->days;
        $dateRange = $request->dateRange;
        if ($days && $days != -1) {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-$days day")),
                "end"   => date("Y-m-d")
            ];
        } else if ($days && $days == -1 && $dateRange) {
            $days = explode(" - ", $dateRange);
            $defaultDay = [
                "begin" => $days[0],
                "end"   => $days[1]
            ];
        }

        $data = self::selectRaw("sum(login_num) as login_num,sum(index_num) as index_num,sum(scan_num) as scan_num,sum(detail_num) as detail_num,sum(period_num) as period_num,sum(deduction_num) as deduction_num,sum(submit_lease_num) as submit_lease_num ,sum(business_num) as business_num,sum(topay_num) as topay_num,sum(dopay_num) as dopay_num,sum(pay_num) as pay_num");
        if ($agentId = $request->agentId) {
            $data->where("province_id", $agentId);
        }
        return $data->whereBetween("process_date", $defaultDay)->first()->toArray();
    }

}
