<?php

namespace Modules\Manage\Repositories\Report;

use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Service\LeaseServiceBalanceLog;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class PostRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LeaseServiceRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'province_id'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LeaseService::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getAgeProfile($provinceId)
    {
        $sql = "elt(interval(age,0,31,41,51),'30岁以下','31-40岁','41-50岁','50岁以上')";
        $ageData = $this->makeModel()->selectRaw($sql . " as age_area,count(id) as num");
        if ($provinceId) {
            $ageData->where("province_id", $provinceId);
        }
        return $ageData->where("age", ">", 0)->groupBy("age_area")->pluck("num", "age_area")->toArray();
    }

    public function getSexProfile($provinceId)
    {
        $sexData = $this->makeModel()->selectRaw("sex, count(id) as num ");
        if ($provinceId) {
            $sexData->where("province_id", $provinceId);
        }
        return $sexData->where("sex", ">", 0)->groupBy("sex")->pluck("num", "sex")->toArray();
    }

    public function getAreaProfile()
    {
        return $this->makeModel()->selectRaw("province_id, count(id) as num ")
            ->where("province_id", ">", 0)->groupBy("province_id")
//            ->where('status',1)
            ->pluck("num", "province_id")->toArray();
    }

    //注册审核趋势数据
    public function registerTrend($request, &$defaultDay)
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
        $crmNum = " sum(case when business_id=0 THEN 0 ELSE 1 END) as crm_num, ";
        $auditedNum = " sum(case when audited_at is null THEN 0 ELSE 1 END) as audited_num, ";
        $data = $this->makeModel()->selectRaw("$crmNum $auditedNum count(id) as apply_num,created_date as date");
        if ($agentId = $request->agentId) {
            $data->where("province_id", $agentId);
        }
        $data->whereBetween("created_date", $defaultDay)->groupBy("created_date")->orderBy("created_date");
        return $data->get()->toArray();
    }

    //注册审核列表
    public function registerTrendList($request)
    {
        $where = [];
        if ($dateRange = $request->dateRange) {
            $time = explode(' - ', $dateRange);
            if ($time[0] == $time[1]) {
                $where[] = ["created_date", $time[0]];
            } else {
                $where[] = ['created_date', '>=', $time[0]];
                $where[] = ['created_date', '<=', $time[1]];
            }
        }
        $crmNum = " sum(case when business_id=0 THEN 0 ELSE 1 END) as crm_num, ";
        $auditedNum = " sum(case when audited_at is null THEN 0 ELSE 1 END) as audited_num, ";
        $data = $this->makeModel()->selectRaw("$crmNum $auditedNum count(id) as apply_num,created_date as date");
        if ($agentId = $request->agentId) {
            $data->where("province_id", $agentId);
        }
        $data->where($where)->groupBy("created_date")->orderBy("created_date", "desc");
        $count = clone $data;
        $count = $count->get()->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();
        return ["data" => $data, "count" => $count];
    }

    //余额金额分布
    public function getBalanceProfile($provinceId, $field, $fieldName)
    {
        $sql = "elt(interval(balance,$field),$fieldName)";
        $ageData = $this->makeModel()->newQuery()->selectRaw($sql . " as balance_area,count(id) as num");
        if ($provinceId) {
            $ageData->where("province_id", $provinceId);
        }
        return $ageData->groupBy("balance_area")->pluck("num", "balance_area")->toArray();
    }

    //各区域余额统计
    public function getBalanceArea()
    {
        return $this->makeModel()->newQuery()->selectRaw("sum(balance) as balance,province_id")
            ->groupBy("province_id")->orderBy("province_id")->pluck("balance", "province_id")->toArray();
    }

    //各区域网点收益分布
    public function getIncomeArea($request, $defaultDay)
    {
        if ($request->days && $request->days != -1) {
            $days = $request->days;
            $defaultDay["begin"] = date("Y-m-d", strtotime("-$days day"));
        } else if ($request->days && $request->days == -1 && $request->dateRange) {
            $days = explode(" - ", $request->dateRange);
            $defaultDay = [
                "begin" => $days[0],
                "end"   => date("Y-m-d H:i:s", strtotime($days[1]) + 86400)
            ];
        }

        return $this->makeModel()->newQuery()->from("lease_services as a")
            ->selectRaw("sum(b.amount) as income,a.province_id")
            ->leftJoin("lease_service_balance_logs as b", "a.id", "=", "b.service_id")
            ->where("b.source", LeaseServiceBalanceLog::SOURCE_FOUR)
            ->whereBetween("b.created_at", $defaultDay)
            ->groupBy("a.province_id")->orderBy("a.province_id")->pluck("income", "province_id")->toArray();
    }

    //各区域网点收益分布 按日期按省份分组
    public function getIncomeAreaByDate($days)
    {
        $defaultDay = [
            "begin" => $days[0],
            "end"   => $days[1] . " 23:59:59"
        ];

        return $this->makeModel()->newQuery()->from("lease_services as a")
            ->selectRaw("sum(b.amount) as income,a.province_id,DATE_FORMAT(b.created_at, '%Y-%m-%d') as date")
            ->leftJoin("lease_service_balance_logs as b", "a.id", "=", "b.service_id")
            ->where("b.source", LeaseServiceBalanceLog::SOURCE_FOUR)
            ->whereBetween("b.created_at", $defaultDay)
            ->groupBy("a.province_id")
            ->groupBy("date")
            ->orderBy("a.province_id")
            ->orderBy("date")
            ->get()
            ->toArray();
    }

    //各区域网点数排行
    public function sort()
    {
        $result = $this->makeModel()->newQuery()->selectRaw(" count(id) as num,province_id ")
            ->groupBy('province_id')
            ->pluck('num','province_id')
            ->toArray();
        arsort($result);
        return $result;
    }

    //各区域网点收益分布
    public function getIncomeAreaRank($request)
    {
        return $this->makeModel()->newQuery()->from("lease_services as a")
            ->selectRaw("sum(b.amount) as income,a.province_id,count(DISTINCT service_id) as service_num")
            ->leftJoin("lease_service_balance_logs as b", "a.id", "=", "b.service_id")
            ->where("b.source", LeaseServiceBalanceLog::SOURCE_FOUR)
            ->groupBy("a.province_id")->orderBy("income",'desc')->get();
    }
}
