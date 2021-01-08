<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/8 15:03
 */

namespace Modules\Manage\Models\Service;

use Modules\Manage\Models\Model;

class LeaseIncomeLog extends Model
{
    //各区域网点收益统计列表
    public function getList($request)
    {
        $req = $request->all();
        $where = [];
        if (isset($req["dateRange"]) && ($dateRange = $req["dateRange"])) {
            $time = explode(' - ', $dateRange);
            if ($time[0] == $time[1]) {
                $where[] = ["date", $time[0]];
            } else {
                $where[] = ['date', '>=', $time[0]];
                $where[] = ['date', '<=', $time[1]];
            }
        }
        $data = self::where($where)->orderBy("date", "desc");
        $count = $data->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();

        $data = $this->formatProvince($data);
        return ["data" => $data, "count" => $count];
    }

    public function formatProvince($data)
    {
        $returnData = [];
        $provinces = allUserProvinces();
        foreach ($data as $datum) {
            $per["全部区域"] = 0;
            $per["date"] = $datum["date"];
            $amountArr = json_decode($datum["json"], true);
            foreach ($provinces as $id => $province) {
                $amount = isset($amountArr[$id]) ? $amountArr[$id] : 0;
                $per[$province] = $amount * 1;
                $per["全部区域"] += $amount;
            }
            $returnData[] = $per;
        }
        return $returnData;
    }
}