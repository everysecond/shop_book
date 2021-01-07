<?php

namespace Modules\Manage\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Kood\Models\depot;
use Modules\Kood\Models\DepotGood;
use Modules\Kood\Models\DepotRecycle;
use Modules\Kood\Models\SaleOrder;
use Modules\Manage\Http\Controllers\Controller;


class KdBasicDataController extends Controller
{
    //仓库库存
    public function basic(Request $request)
    {
//         $date_time = time();
//         $timetype = strtotime($this->timeBegin($request->type));
         // 省份选择
         $site_id = $request->manager->site_id;

         //仓库个数
         $depot_num = depot::where('status', 1);

         //销售,退回,售后电池
         $depot_goods_sale = DepotGood::selectRaw("sum(kd_depot_goods.sku) as sku,kd_goods.type")
            ->leftJoin('goods', 'depot_goods.goods_id', '=', 'goods.id')
            ->where('depot_goods.available',1)
            ->groupBy("goods.type");

         //回收电池
         $depot_recycles = new DepotRecycle();

         if ($site_id){
              $depot_recycles = $depot_recycles->where('site_id',$site_id);
              $depot_goods_sale =  $depot_goods_sale->where("kd_depot_goods.site_id",$site_id);
              $depot_num = $depot_num->where('site_id',$site_id);
         }

         $depot_num = $depot_num->count();
         $depot_goods_sale = $depot_goods_sale->pluck('sku','type');
         $depot_recycles = $depot_recycles->sum("sku");

         if (isset($depot_goods_sale[1])){
             $depot_goods_sale_1 = $depot_goods_sale[1];
         }else{
             $depot_goods_sale_1 = 0;
         }

        if (isset($depot_goods_sale[2])){
            $depot_goods_sale_2 = $depot_goods_sale[2];
        }else{
            $depot_goods_sale_2 = 0;
        }


        if (isset($depot_goods_sale[3])){
            $depot_goods_sale_3 = $depot_goods_sale[3];
        }else{
            $depot_goods_sale_3 = 0;
        }
        $data[0]['name'] = "仓库数量(个)";
        $data[0]['value'] = $depot_num;
        $data[1]['name'] = "销售电池(组)";
        $data[1]['value'] = $depot_goods_sale_1;
        $data[2]['name'] = "退回电池(组)";
        $data[2]['value'] = $depot_goods_sale_2;
        $data[3]['name'] = "售后专用(组)";
        $data[3]['value'] = $depot_goods_sale_3;
        $data[4]['name'] = "回收电池(吨)";
        $data[4]['value'] = round($depot_recycles/1000,2);

        return result('', 1, $data);

    }

  //电池型号占比
    public function batteyType(Request $request)
    {
        //当前登陆人可查看区域
        $site_id = $request->manager->site_id;

        $site_id = !$site_id ? $request->get('site_id', 0) : $site_id;
        //选择时间范围
            $begin = strtotime($this->timeSelect($request->get('type', 1)));

        $depot_goods_sale = SaleOrder::selectRaw("sum(kd_sale_order_goods.num) as num,kd_goods.name")
            ->leftJoin('sale_order_goods', 'sale_order_goods.order_id', '=', 'sale_orders.id')
            ->leftJoin('goods', 'sale_order_goods.goods_id', '=', 'goods.id')
            ->where('sale_order_goods.goods_id','>',0)
            ->where('sale_order_goods.price','>',0)
            ->whereIn('sale_orders.status',[221,232,242,252,281])
            ->whereNull('sale_order_goods.deleted_at');

        if($site_id){
            $depot_goods_sale = $depot_goods_sale->where('sale_orders.site_id',$site_id);

        }
        $depot_goods_sale = $depot_goods_sale->groupBy("sale_order_goods.goods_id")->when($begin, function ($query) use ($begin) {
            $query->where('sale_orders.confirmed_at', '>', $begin);
        })
        ->orderBy("num","desc")->get()->toArray();

        $data = [];
        if ($depot_goods_sale){
            $data[5]['value'] = 0;
            foreach ($depot_goods_sale as $key=>$value){
                if ($key>4){
                    $data[5]['name'] = "其他";
                    $data[5]['value']+= $value['num'];
                }else{
                    if ($arr = preg_split("/([a-zA-Z0-9]+)/", $value['name'], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE)){
                        $data[$key]['name'] = $arr[1];
                        $data[$key]['value'] = $value['num'];
                    }

                }

            }
        }
        sort($data);
        return result('', 1, $data);
    }

    /**
     * 电池销售趋势
     * @param Request $request
     *                type:时间类型
     *                site_id:省id
     * @return false|string
     */
    public function saleTrend(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $format = $request->get('type', 1) <= 2 ? 'FROM_UNIXTIME(created_at,"%Y-%m-%d")' : 'FROM_UNIXTIME(created_at,"%Y-%m")';
            $data = SaleOrder::query()
                ->selectRaw("sum(total_amount) as money,sum(num) as num,$format as date")
                ->when($begin, function ($query) use ($begin) {
                    $query->where('confirmed_at', '>', strtotime($begin));
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->whereIn('status',[221,232,242,252,281])
                ->groupBy("date")
                ->orderBy('date')->get()->toArray();
            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }


    /**
     * 电池销售排行（区域）
     * @param Request $request
     *                type:时间类型
     *                site_id:省id
     * @return false|string
     */
    public function batterySale(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $groupByColumn = $provinceId ? 'agent_id' : 'site_id';
            $data = SaleOrder::query()
                ->selectRaw('sum(num) as num,agent_id,site_id')
                ->when($begin, function ($query) use ($begin) {
                    $query->where('created_at', '>', strtotime($begin));
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->whereIn('status',[221,232,242,252,281])
                ->groupBy($groupByColumn)
                ->orderBy('num', 'desc')->pluck('num', $groupByColumn)->toArray();

            $result = array();
            if ($groupByColumn == 'site_id') {
                $sites = siteCache();
                foreach ($data as $id => $datum) {
                    $result[] = [
                        'name'  => Arr::get($sites, $id),
                        'value' => $datum
                    ];
                }
            } else {
                $agents = Arr::get(kdAgentArr(), $provinceId + 100000);
                $dicts = Arr::get($agents, 'city_arr');
                $arr = array();
                foreach ($dicts as $id => $name) {
                    foreach ($data as $agentId => $datum) {
                        if (in_array($agentId, Arr::get($agents, "city_child_arr.$id"))) {
                            $arr[$name] = isset($arr[$name]) ? $arr[$name] + $datum : $datum;
                        }
                    }
                }
                arsort($arr);
                foreach ($arr as $name => $value) {
                    $result[] = [
                        'name'  => $name,
                        'value' => $value
                    ];
                }
            }
            return result('', 1, $result);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }


    /**
     * 电池销售排行（业务员）
     * @param Request $request
     *                type:时间类型
     *                site_id:省id
     * @return false|string
     */
    public function batterySaleman(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $groupByColumn = $provinceId ? 'agent_id' : 'site_id';

            $data = SaleOrder::query()
                ->selectRaw('sum(num) as num,manager_id')
                ->when($begin, function ($query) use ($begin) {
                    $query->where('created_at', '>', strtotime($begin));
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->whereIn('status',[232,242,252,281])
                ->groupBy('manager_id')
                ->orderBy('num', 'desc')->pluck('num', 'manager_id')->toArray();

            $result = array();
            $managers = \DB::connection('mysql_kd')->table('agent_managers')
                ->whereIn('id', array_keys($data))
                ->pluck('name', 'id')
                ->toArray();

            foreach ($data as $id => $datum) {
                $result[] = [
                    'name' => Arr::get($managers, $id),
                    'value' => $datum
                ];
            }

            return result('', 1, $result);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }
}
