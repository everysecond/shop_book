<?php

namespace Modules\Manage\Http\Controllers\Kood;

use \Exception;
use Illuminate\Http\Request;
use Modules\Kood\Models\DepotGood;
use Modules\Kood\Models\DepotRecycle;
use Modules\Kood\Models\RecycleOrder;
use Modules\Kood\Models\SaleOrder;
use Modules\Kood\Models\Site;
use Modules\Manage\Http\Controllers\Controller;

class BigViewController extends Controller
{
    //网点数据按时间分布
    public function stockArea($type)
    {
        $sites = Site::sites();
        if($type == 'sale'){
            $data = DepotGood::query()
                ->from('depot_goods as a')
                ->selectRaw('sum(kd_a.sku) as sku_all,kd_a.site_id')
                ->leftJoin('goods as b', 'a.goods_id', '=', 'b.id')
                ->where('b.category_id', 1)
                ->whereNull('b.deleted_at')
                ->whereNotIn('a.site_id', [4, 11])
                ->groupBy('a.site_id')
                ->orderBy('a.site_id', 'desc')
                ->pluck('sku_all', 'site_id')
                ->toArray();
        }else{
            $data = DepotRecycle::query()
                ->selectRaw('sum(sku/1000) as sku_all,site_id')
                ->whereNotIn('site_id', [4, 11])
                ->groupBy('site_id')
                ->pluck('sku_all', 'site_id')
                ->toArray();
        }


        $siteIds = array_keys($sites);
        $data = array_replace(array_flip($siteIds), $data);

        $data = [
            'categories' => array_values($sites),
            'series'     => [
                ['type' => 'bar', 'data' => array_round(array_values($data))]
            ]
        ];
        return result("", 1, $data);
    }

    //租点-实时数据
    public function actualData()
    {
        $time = strtotime(date('Y-m-d'));
        $sale = SaleOrder::query()
            ->selectRaw('count(id) as order_num,sum(total_amount) as total_amount')
            ->where('created_at', '>', $time)
            ->whereIn('status', [221, 232, 242, 252, 281])
            ->first();
        $recycle = RecycleOrder::query()
            ->selectRaw('count(id) as order_num,sum(weight) as weight')
            ->where('created_at', '>', $time)
            ->where('status', 288)
            ->first();
        $data = [
            [
                'prefixText' => '今日销售订单数(个)',
                'data'       => $sale->order_num
            ],
            [
                'prefixText' => '今日销售额(元)',
                'data'       => $sale->total_amount
            ],
            [
                'prefixText' => '今日回收订单数(个)',
                'data'       => $recycle->order_num
            ],
            [
                'prefixText' => '今日回收量(吨)',
                'data'       => $recycle->weight / 1000
            ],
        ];
        return result("", 1, $data);
    }

    //租点-实时数据
    public function inventory()
    {
        $sale = DepotGood::query()
            ->from('depot_goods as a')
            ->leftJoin('goods as b', 'a.goods_id', '=', 'b.id')
            ->where('b.category_id', 1)
            ->whereNull('b.deleted_at')
            ->sum('a.sku');
        $recycle = DepotRecycle::query()
            ->sum('sku');
        $data = [
            [
                'prefixText' => '销售电池(组):',
                'data'       => $sale
            ],
            [
                'prefixText' => '回收电池(吨):',
                'data'       => $recycle/1000
            ],
        ];
        return result("", 1, $data);
    }

    public function recycleRank()
    {
        $sites = Site::sites();
        $recycles = RecycleOrder::query()
            ->selectRaw('count(id) as order_num,sum(weight/1000) as weight,site_id')
            ->where('status', 288)
            ->whereNotIn('site_id', [4, 11])
            ->groupBy('site_id')
            ->orderBy('weight','desc')
            ->get()
            ->toArray();
        foreach ($recycles as &$recycle) {
            $recycle['site_name'] = $sites[$recycle['site_id']];
            $recycle['weight'] = round($recycle['weight'],2);
        }
        return result("", 1, $recycles);
    }
}
