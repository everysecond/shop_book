<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/9/27 15:42
 */

namespace Modules\Manage\Models\Report;

use Modules\Manage\Models\Model;

class LeaseAppDownLog extends Model
{
    const CHANNEL_ARR = [
        'yingyongbao' => '应用宝',
        'baidu'       => '百度',
        'xiaomi'      => '小米',
        'huawei'      => '华为',
        'oppo'        => 'OPPO',
        'vivo'        => 'VIVO',
        'sougou'      => '搜狗',
        'wandoujia'   => '豌豆荚'
    ];

    //下载趋势
    public static function trend($request, &$defaultDay, $appType = 1)
    {
        $req = $request->all();
        if (isset($req["days"]) && $req["days"] != -1) {
            $days = $req["days"];
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-$days day")),
                "end"   => date("Y-m-d")
            ];
        } elseif (isset($req["days"]) && $req["days"] == -1 && isset($req["dateRange"]) && $req["dateRange"]) {
            $days = explode(" - ", $req["dateRange"]);
            $defaultDay = [
                "begin" => $days[0],
                "end"   => $days[1]
            ];
        }
        $data = self::selectRaw(" date,total as num ")->where("app_type", $appType);
        $data->whereBetween("date", $defaultDay)->groupBy("date")->orderBy("date");
        $data = $data->get()->toArray();
        return $data;
    }

    //下载量排行
    public function getSortProfile($request, $appType = 1)
    {
        $req = $request->all();
        $where = ['app_type' => $appType];
        if (isset($req["days"]) && $req["days"] != -1) {
            $days = $req["days"];
            $where[] = ['date', '>=', date("Y-m-d", strtotime("-$days day"))];
            $where[] = ['date', '<=', date("Y-m-d")];
        } else if (isset($req["days"]) && $req["days"] == -1 && isset($req["dateRange"]) && $req["dateRange"]) {
            $days = explode(" - ", $req["dateRange"]);
            $where[] = ['date', '>=', $days[0]];
            $where[] = ['date', '<=', $days[1]];
        }

        $data = self::query()->where($where)->get()->toArray();
        $result = [
            '应用宝'  => 0,
            '百度'   => 0,
            '小米'   => 0,
            '华为'   => 0,
            'OPPO' => 0,
            'VIVO' => 0,
            '搜狗'   => 0,
            '豌豆荚'  => 0
        ];
        $channelArr = self::CHANNEL_ARR;
        foreach ($data as &$datum) {
            $channels = json_decode($datum['channel_json'], true);
            foreach ($channels as $channel => $num) {
                if (isset($channelArr[$channel]) && isset($result[$channelArr[$channel]])) {
                    $result[$channelArr[$channel]] += $num;
                }
            }
        }
        arsort($result);
        return $result;
    }

    //下载统计表
    public function getList($request, $appType = 1)
    {
        $req = $request->all();
        $where = ['app_type' => $appType];
        if (isset($req["dateRange"]) && ($dateRange = $req["dateRange"])) {
            $time = explode(' - ', $dateRange);
            if ($time[0] == $time[1]) {
                $where[] = ["date", $time[0]];
            } else {
                $where[] = ['date', '>=', $time[0]];
                $where[] = ['date', '<=', $time[1]];
            }
        }
        $data = self::query()->where($where)->orderBy("date", "desc");
        $count = $data->count();
        $page = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 15;
        $offset = ($page - 1) * $limit;
        $data = $data->offset($offset)->limit($limit)->get()->toArray();
        foreach ($data as &$datum) {
            foreach (self::CHANNEL_ARR as $channel=>$name) {
                $datum[$channel] = 0;
            }
            $channels = json_decode($datum['channel_json'], true);
            foreach ($channels as $channel => $num) {
                $datum[$channel] = $num;
            }
        }
        return ["data" => $data, "count" => $count];
    }
}