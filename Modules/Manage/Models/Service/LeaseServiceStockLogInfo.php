<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/10/10 08:39
 */

namespace Modules\Manage\Models\Service;

use Modules\Manage\Models\Model;

class LeaseServiceStockLogInfo extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';
    //补货
    const STOCK_TYPE_ONE = 1;
    //出货
    const STOCK_TYPE_TWO = 2;
    //电池类型 租赁
    const BATTERY_TYPE_ONE = 1;
    //废旧
    const BATTERY_TYPE_TWO = 2;
    //退回
    const BATTERY_TYPE_THREE = 3;
    //回收的废旧电池
    const LEASE_TYPE_ZERO = 0;
    //全新
    const LEASE_TYPE_ONE = 1;
    //备用
    const LEASE_TYPE_TWO = 2;
    //维护
    const LEASE_TYPE_THREE = 3;
    //退回
    const LEASE_TYPE_FOUR = 4;
    //报废
    const LEASE_TYPE_FIVE = 5;

    //补货趋势
    public function trend($request)
    {
        $defaultDay = timeTypeDate($request);
        $stockType = $request->stock_type ?? '1';
        $where = ['stock_type' => $stockType];
        $where['battery_type'] = self::BATTERY_TYPE_ONE;
        if ($stockType == 2) {
            $where['relation_type'] = 'App\Models\ServiceRetrieveModel';
        } else {
            $where['relation_type'] = 'App\Models\ServiceSupplyModel';
        }
        if ($type = $request->type) {
            $where['lease_type'] = $type;
        }
        $data = self::query()->selectRaw('count(DISTINCT relation_id) as apply_num,sum(num) as battery_num,date')
            ->whereIn('lease_type', [self::LEASE_TYPE_ONE, self::LEASE_TYPE_TWO])
            ->whereBetween('date', $defaultDay)
            ->where($where)
            ->groupBy('date')
            ->get()
            ->toArray();
        $actuallyBegin = isset($data[0]) ? $data[0]["date"] : $defaultDay["begin"];
        $dateRange = getDateRange($actuallyBegin, $defaultDay["end"]);
        $lines = ['apply_num' => '申请数', 'battery_num' => '电池组数'];
        $series = [];
        $defaultNumArr = [];
        foreach ($dateRange as $day) {
            $defaultNumArr[$day] = 0;
        }
        foreach ($lines as $code => $line) {
            $series[$code] = [
                "name"       => $line,
                "type"       => 'line',
                "stack"      => $line,
                "symbolSize" => 8,
                "symbol"     => 'circle',
                "data"       => $defaultNumArr
            ];
            foreach ($data as $datum) {
                $series[$code]["data"][$datum["date"]] = $datum[$code];
            }
            $series[$code]["data"] = array_values($series[$code]["data"]);
        }
        $days = [];
        foreach ($dateRange as $xAxi) {
            $days[] = date("m-d", strtotime($xAxi));
        }
        return [
            "legend" => array_values($lines),
            "xAxis"  => $days,
            "series" => array_values($series)
        ];
    }

    //补货分析
    public function analysis($request)
    {
        $defaultDay = timeTypeDate($request);
        $stockType = $request->stock_type ?? '1';
        $where = ['stock_type' => $stockType];
        $where['battery_type'] = self::BATTERY_TYPE_ONE;
        if ($stockType == 2) {
            $where['relation_type'] = 'App\Models\ServiceRetrieveModel';
        } else {
            $where['relation_type'] = 'App\Models\ServiceSupplyModel';
        }
        if ($type = $request->type) {
            $where['lease_type'] = $type;
        }
        $data = self::query()->selectRaw('count(DISTINCT relation_id) as apply_num,sum(num) as battery_num,province_id')
            ->whereIn('lease_type', [self::LEASE_TYPE_ONE, self::LEASE_TYPE_TWO])
            ->whereBetween('date', $defaultDay)
            ->where($where)
            ->groupBy('province_id')
            ->orderBy('province_id')
            ->where('province_id', '>', 0)
            ->get()
            ->toArray();
        $stockType = $stockType == 1 ? '补货' : '退库';
        $lines = ['apply_num' => $stockType . '申请数', 'battery_num' => $stockType . '电池组数'];
        $series = [];
        $provinces = allLeaseProvinces();
        $defaultAreaArr = [];
        foreach ($provinces as $id => $name) {
            $defaultAreaArr[$id] = 0;
        }
        foreach ($lines as $code => $line) {
            $series[$code] = [
                "name" => $line,
                "type" => 'bar',
                "data" => $defaultAreaArr
            ];
            foreach ($data as $datum) {
                $series[$code]["data"][$datum["province_id"]] = $datum[$code];
            }
            $series[$code]["data"] = array_values($series[$code]["data"]);
        }
        return [
            "legend" => array_values($lines),
            "xAxis"  => array_values($provinces),
            "series" => array_values($series)
        ];
    }

    //补货统计列表
    public function getList($request)
    {
        $req = $request->all();
        $stockType = $request->stock_type ?? '1';
        $where = ['stock_type' => $stockType];
        $where['battery_type'] = self::BATTERY_TYPE_ONE;
        if ($stockType == 2) {
            $where['relation_type'] = 'App\Models\ServiceRetrieveModel';
        } else {
            $where['relation_type'] = 'App\Models\ServiceSupplyModel';
        }
        if ($type = $request->type) {
            $where['lease_type'] = $type;
        }
        if (isset($req["dateRange"]) && ($dateRange = $req["dateRange"])) {
            $time = explode(' - ', $dateRange);
            if ($time[0] == $time[1]) {
                $where[] = ["date", $time[0]];
            } else {
                $where[] = ['date', '>=', $time[0]];
                $where[] = ['date', '<=', $time[1]];
            }
        }

        $selectNum = $request->num_type == 2 ? 'num' : 'relation_id';
        $sql = '';
        $provinces = allLeaseProvinces();
        foreach ($provinces as $id => $province) {
            if ($selectNum == 'num') {
                $sql .= "sum(IF(province_id = $id,$selectNum,0)) as $province,";
            } else {
                $sql .= "count(DISTINCT IF(province_id = $id,$selectNum,null)) as $province,";
            }
        }

        if ($selectNum == 'num') {
            $all = "sum(IF(province_id >0,$selectNum,0)) as 全部区域";
        } else {
            $all = "count(DISTINCT IF(province_id >0,$selectNum,null)) as 全部区域";
        }

        $data = self::query()->selectRaw("$sql $all, date")
            ->whereIn('lease_type', [self::LEASE_TYPE_ONE, self::LEASE_TYPE_TWO])
            ->where($where)
            ->groupBy('date')
            ->orderBy("date", "desc");
        $clone = clone $data;
        $count = $clone->get()->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();
        return ["data" => $data, "count" => $count];
    }

    //退回趋势
    public function returnTrend($request)
    {
        $defaultDay = timeTypeDate($request);
        $where = ['stock_type' => self::BATTERY_TYPE_TWO];
        $where['battery_type'] = self::BATTERY_TYPE_THREE;
        $where['relation_type'] = 'App\Models\ServiceRetrieveModel';
        $data = self::query()->selectRaw('count(DISTINCT relation_id) as apply_num,sum(num) as battery_num,date')
            ->whereBetween('date', $defaultDay)
            ->where($where)
            ->groupBy('date')
            ->get()
            ->toArray();
        $actuallyBegin = isset($data[0]) ? $data[0]["date"] : $defaultDay["begin"];
        $dateRange = getDateRange($actuallyBegin, $defaultDay["end"]);
        $lines = ['apply_num' => '申请数', 'battery_num' => '电池组数'];
        $series = [];
        $defaultNumArr = [];
        foreach ($dateRange as $day) {
            $defaultNumArr[$day] = 0;
        }
        foreach ($lines as $code => $line) {
            $series[$code] = [
                "name"       => $line,
                "type"       => 'line',
                "stack"      => $line,
                "symbolSize" => 8,
                "symbol"     => 'circle',
                "data"       => $defaultNumArr
            ];
            foreach ($data as $datum) {
                $series[$code]["data"][$datum["date"]] = $datum[$code];
            }
            $series[$code]["data"] = array_values($series[$code]["data"]);
        }
        $days = [];
        foreach ($dateRange as $xAxi) {
            $days[] = date("m-d", strtotime($xAxi));
        }
        return [
            "legend" => array_values($lines),
            "xAxis"  => $days,
            "series" => array_values($series)
        ];
    }

    //退回分析
    public function returnAnalysis($request)
    {
        $defaultDay = timeTypeDate($request);
        $where = ['stock_type' => self::BATTERY_TYPE_TWO];
        $where['battery_type'] = self::BATTERY_TYPE_THREE;
        $where['relation_type'] = 'App\Models\ServiceRetrieveModel';
        $data = self::query()->selectRaw('count(DISTINCT relation_id) as apply_num,sum(num) as battery_num,province_id')
            ->whereBetween('date', $defaultDay)
            ->where($where)
            ->groupBy('province_id')
            ->orderBy('province_id')
            ->where('province_id', '>', 0)
            ->get()
            ->toArray();
        $lines = ['apply_num' => '退回申请数', 'battery_num' => '退回电池组数'];
        $series = [];
        $provinces = allLeaseProvinces();
        $defaultAreaArr = [];
        foreach ($provinces as $id => $name) {
            $defaultAreaArr[$id] = 0;
        }
        foreach ($lines as $code => $line) {
            $series[$code] = [
                "name" => $line,
                "type" => 'bar',
                "data" => $defaultAreaArr
            ];
            foreach ($data as $datum) {
                $series[$code]["data"][$datum["province_id"]] = $datum[$code];
            }
            $series[$code]["data"] = array_values($series[$code]["data"]);
        }
        return [
            "legend" => array_values($lines),
            "xAxis"  => array_values($provinces),
            "series" => array_values($series)
        ];
    }

    //退回统计列表
    public function returnTable($request)
    {
        $req = $request->all();
        $where = ['stock_type' => self::BATTERY_TYPE_TWO];
        $where['battery_type'] = self::BATTERY_TYPE_THREE;
        $where['relation_type'] = 'App\Models\ServiceRetrieveModel';
        if (isset($req["dateRange"]) && ($dateRange = $req["dateRange"])) {
            $time = explode(' - ', $dateRange);
            if ($time[0] == $time[1]) {
                $where[] = ["date", $time[0]];
            } else {
                $where[] = ['date', '>=', $time[0]];
                $where[] = ['date', '<=', $time[1]];
            }
        }

        $selectNum = $request->num_type == 2 ? 'num' : 'relation_id';
        $sql = '';
        $provinces = allLeaseProvinces();
        foreach ($provinces as $id => $province) {
            if ($selectNum == 'num') {
                $sql .= "sum(IF(province_id = $id,$selectNum,0)) as $province,";
            } else {
                $sql .= "count(DISTINCT IF(province_id = $id,$selectNum,null)) as $province,";
            }
        }

        if ($selectNum == 'num') {
            $all = "sum(IF(province_id >0,$selectNum,0)) as 全部区域";
        } else {
            $all = "count(DISTINCT IF(province_id >0,$selectNum,null)) as 全部区域";
        }

        $data = self::query()->selectRaw("$sql $all, date")
            ->where($where)
            ->groupBy('date')
            ->orderBy("date", "desc");
        $clone = clone $data;
        $count = $clone->get()->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();
        return ["data" => $data, "count" => $count];
    }

    //回收趋势
    public function recycleTrend($request)
    {
        $defaultDay = timeTypeDate($request);
        $where = ['stock_type' => self::BATTERY_TYPE_TWO];
        $where['battery_type'] = self::BATTERY_TYPE_TWO;
        $where['relation_type'] = 'App\Models\ServiceRetrieveModel';
        $data = self::query()->selectRaw('count(DISTINCT relation_id) as apply_num,sum(num) as battery_num,date')
            ->whereBetween('date', $defaultDay)
            ->where($where)
            ->groupBy('date')
            ->get()
            ->toArray();
        $actuallyBegin = isset($data[0]) ? $data[0]["date"] : $defaultDay["begin"];
        $dateRange = getDateRange($actuallyBegin, $defaultDay["end"]);
        $lines = ['apply_num' => '申请数', 'battery_num' => '电池只数'];
        $series = [];
        $defaultNumArr = [];
        foreach ($dateRange as $day) {
            $defaultNumArr[$day] = 0;
        }
        foreach ($lines as $code => $line) {
            $series[$code] = [
                "name"       => $line,
                "type"       => 'line',
                "stack"      => $line,
                "symbolSize" => 8,
                "symbol"     => 'circle',
                "data"       => $defaultNumArr
            ];
            foreach ($data as $datum) {
                $series[$code]["data"][$datum["date"]] = $datum[$code];
            }
            $series[$code]["data"] = array_values($series[$code]["data"]);
        }
        $days = [];
        foreach ($dateRange as $xAxi) {
            $days[] = date("m-d", strtotime($xAxi));
        }
        return [
            "legend" => array_values($lines),
            "xAxis"  => $days,
            "series" => array_values($series)
        ];
    }

    //回收分析
    public function recycleAnalysis($request)
    {
        $defaultDay = timeTypeDate($request);
        $where = ['stock_type' => self::BATTERY_TYPE_TWO];
        $where['battery_type'] = self::BATTERY_TYPE_TWO;
        $where['relation_type'] = 'App\Models\ServiceRetrieveModel';
        $data = self::query()->selectRaw('count(DISTINCT relation_id) as apply_num,sum(num) as battery_num,province_id')
            ->whereBetween('date', $defaultDay)
            ->where($where)
            ->groupBy('province_id')
            ->orderBy('province_id')
            ->where('province_id', '>', 0)
            ->get()
            ->toArray();
        $lines = ['apply_num' => '回收申请数', 'battery_num' => '回收电池只数'];
        $series = [];
        $provinces = allLeaseProvinces();
        $defaultAreaArr = [];
        foreach ($provinces as $id => $name) {
            $defaultAreaArr[$id] = 0;
        }
        foreach ($lines as $code => $line) {
            $series[$code] = [
                "name" => $line,
                "type" => 'bar',
                "data" => $defaultAreaArr
            ];
            foreach ($data as $datum) {
                $series[$code]["data"][$datum["province_id"]] = $datum[$code];
            }
            $series[$code]["data"] = array_values($series[$code]["data"]);
        }
        return [
            "legend" => array_values($lines),
            "xAxis"  => array_values($provinces),
            "series" => array_values($series)
        ];
    }

    //回收统计列表
    public function recycleTable($request)
    {
        $req = $request->all();
        $where = ['stock_type' => self::BATTERY_TYPE_TWO];
        $where['battery_type'] = self::BATTERY_TYPE_TWO;
        $where['relation_type'] = 'App\Models\ServiceRetrieveModel';
        if (isset($req["dateRange"]) && ($dateRange = $req["dateRange"])) {
            $time = explode(' - ', $dateRange);
            if ($time[0] == $time[1]) {
                $where[] = ["date", $time[0]];
            } else {
                $where[] = ['date', '>=', $time[0]];
                $where[] = ['date', '<=', $time[1]];
            }
        }

        $selectNum = $request->num_type == 2 ? 'num' : 'relation_id';
        $sql = '';
        $provinces = allLeaseProvinces();
        foreach ($provinces as $id => $province) {
            if ($selectNum == 'num') {
                $sql .= "sum(IF(province_id = $id,$selectNum,0)) as $province,";
            } else {
                $sql .= "count(DISTINCT IF(province_id = $id,$selectNum,null)) as $province,";
            }
        }

        if ($selectNum == 'num') {
            $all = "sum(IF(province_id >0,$selectNum,0)) as 全部区域";
        } else {
            $all = "count(DISTINCT IF(province_id >0,$selectNum,null)) as 全部区域";
        }

        $data = self::query()->selectRaw("$sql $all, date")
            ->where($where)
            ->groupBy('date')
            ->orderBy("date", "desc");
        $clone = clone $data;
        $count = $clone->get()->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();
        return ["data" => $data, "count" => $count];
    }
}