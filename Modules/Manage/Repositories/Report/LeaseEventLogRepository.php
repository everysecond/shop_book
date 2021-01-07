<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/9 15:47
 */

namespace Modules\Manage\Repositories\Report;


use Modules\Manage\Models\Service\LeaseEventLog;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class LeaseEventLogRepository extends BaseRepository
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
        return LeaseEventLog::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    //注册审核趋势数据
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

        $data = $this->makeModel()->newQuery()->selectRaw("sum(times) as num,date,page_url");
        if ($agentId = $request->agentId) {
            $data->where("province_id", $agentId);
        }
        return $data->whereBetween("date", $defaultDay)->groupBy("date")
            ->groupBy("page_url")->orderBy("date")->get()->toArray();
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

        $data = $this->makeModel()->newQuery()->selectRaw("sum(times) as num,page_url");
        if ($agentId = $request->agentId) {
            $data->where("province_id", $agentId);
        }
        return $data->whereBetween("date", $defaultDay)->groupBy("page_url")->get()->toArray();
    }

    //注册审核列表
    public function activeList($request)
    {
        $where = [];
        if ($dateRange = $request->dateRange) {
            $time = explode(' - ', $dateRange);
            if ($time[0] == $time[1]) {
                $where[] = ["date", $time[0]];
            } else {
                $where[] = ['date', '>=', $time[0]];
                $where[] = ['date', '<=', $time[1]];
            }
        }
        $date = $this->makeModel()->newQuery()->selectRaw("DISTINCT date")->where($where);

        $clone = clone $date;

        $count = $clone->get()->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $dateArr = $date->orderBy("date", "desc")->offset($offset)->limit($limit)->pluck("date")->toArray();

        $data = $this->makeModel()->newQuery()
            ->selectRaw("page_url,date,sum(user_num) as user_num,sum(times) as times")
            ->whereIn("date", $dateArr);
        if ($agentId = $request->agentId) {
            $data->where("province_id", $agentId);
        }
        $data = $data->groupBy("date")->groupBy("page_url")
            ->orderBy("date", "desc")->get()->toArray();
        $return = [];
        $urlArr = config("global.active_event_url");
        unset($urlArr["account/login"]);
        foreach ($dateArr as $day) {
            $return[$day] = ["date" => $day];
            foreach ($urlArr as $url) {
                $return[$day][$url] = 0;
            }
        }
        $field = $request->type == 1 ? "times" : "user_num";
        foreach ($data as $datum) {
            $return[$datum["date"]][$datum["page_url"]] = $datum[$field];
        }


        return ["data" => $return, "count" => $count];
    }
}