<?php

namespace Modules\Manage\Models\Service;
use Modules\Manage\Models\Model;

class LeaseServiceWithdrawLog extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';
    public function contract()
    {

    }

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

        $data = $this->formatProvince($data,$req['province_id']);
        return ["data" => $data, "count" => $count];
    }

    public function formatProvince($data,$type = 1)
    {
        $returnData = [];
        $provinces = allUserProvinces();

        foreach ($data as $datum) {
            $per["全部区域"] = 0;
            $per["date"] = $datum["date"];
            if ($type == 1){
                $amountArr = json_decode($datum["json_amount"], true);
            }else{
                $amountArr = json_decode($datum["json_num"], true);
            }

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
