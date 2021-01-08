<?php

namespace Modules\Manage\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Kood\Models\Depot;
use Modules\Kood\Models\DepotRecycle;
use Modules\Kood\Models\DepotRecycleSend;
use Modules\Kood\Models\RecycleOrder;
use Modules\Kood\Models\Site;
use Modules\Manage\Http\Controllers\Controller;

class KdRecycleController extends Controller
{
    /**
     * 未完成回收订单区域排行
     * @param Request $request
     *                type:时间类型
     *                site_id:省id
     * @return false|string
     */
    public function unfinishedRank(Request $request)
    {
        try {
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $groupByColumn = $provinceId ? 'agent_id' : 'site_id';

            $type = $request->get('data_type', 1);//默认1 预约重量
            $value = $type == 1 ? '(sum(weight)) as value' : 'count(id) as value';
            $data = RecycleOrder::query()
                ->selectRaw("$value,agent_id,site_id")
                ->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->whereIn('status', [111, 132, 242, 261])
                ->groupBy($groupByColumn)
                ->orderBy('value', 'desc')->pluck('value', $groupByColumn)->toArray();

            $result = array();
            if ($groupByColumn == 'site_id') {
                $sites = siteCache();
                foreach ($data as $id => $datum) {
                    $result[] = [
                        'name'  => Arr::get($sites, $id),
                        'value' => round($datum)
                    ];
                }
            } else {
                $agents = Arr::get(kdAgentArr(), $provinceId + 100000);
                $dicts = Arr::get($agents, 'city_arr');
                $arr = array();
                foreach ($dicts as $id => $name) {
                    $idArr = Arr::get($agents, "city_child_arr.$id");
                    foreach ($data as $agentId => $datum) {
                        if (in_array($agentId, $idArr)) {
                            $arr[$name] = isset($arr[$name]) ? $arr[$name] + $datum : $datum;
                        }
                    }
                }
                arsort($arr);
                foreach ($arr as $name => $value) {
                    $result[] = [
                        'name'  => $name,
                        'value' => round($value)
                    ];
                }
            }
            return result('', 1, $result);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 回收单类型占比
     * @param Request $request
     *                type:时间类型
     *                site_id:省id
     * @return false|string
     */
    public function recycleTypePie(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $data = RecycleOrder::query()
                ->selectRaw('if(order_type = 1,"普通回收","以旧换新") as name,count(id) as value')
                ->when($begin, function ($query) use ($begin) {
                    $query->where('created_at', '>', strtotime($begin));
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->where('status', 288)
                ->groupBy('name')
                ->orderBy('value', 'desc')->get()->toArray();
            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 电池回收排行（区域）
     * @param Request $request
     *                type:时间类型
     *                site_id:省id
     * @return false|string
     */
    public function recycleAreaRank(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $groupByColumn = $provinceId ? 'agent_id' : 'site_id';
            $data = RecycleOrder::query()
                ->selectRaw('sum(weight) as weight,sum(price) as price,count(id) as num,agent_id,site_id')
                ->when($begin, function ($query) use ($begin) {
                    $query->where('created_at', '>', strtotime($begin));
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->where('status', 288)
                ->groupBy($groupByColumn)
                ->orderBy('weight', 'desc')->get()->toArray();

            $result = array();
            if ($groupByColumn == 'site_id') {
                $sites = siteCache();
                foreach ($data as $datum) {
                    $result[] = [
                        'name'   => Arr::get($sites, $datum[$groupByColumn]),
                        'weight' => round($datum['weight'] / 1000),
                        'price'  => round($datum['price']),
                        'num'    => $datum['num']
                    ];
                }
            } else {
                $agents = Arr::get(kdAgentArr(), $provinceId + 100000);
                $dicts = Arr::get($agents, 'city_arr');
                $arr = array();
                foreach ($dicts as $id => $name) {
                    $idArr = Arr::get($agents, "city_child_arr.$id");
                    foreach ($data as $datum) {
                        if (in_array($datum[$groupByColumn], $idArr)) {
                            $arr[$name]['weight'] = isset($arr[$name]['weight']) ? $arr[$name]['weight'] + $datum['weight'] : $datum['weight'];
                            $arr[$name]['price'] = isset($arr[$name]['price']) ? $arr[$name]['price'] + $datum['price'] : $datum['price'];
                            $arr[$name]['num'] = isset($arr[$name]['num']) ? $arr[$name]['num'] + $datum['num'] : $datum['num'];
                        }
                    }
                }
                arsort($arr);
                foreach ($arr as $name => $value) {
                    $result[] = [
                        'name'   => $name,
                        'weight' => round((isset($value['weight']) ? $value['weight'] : 0) / 1000),
                        'price'  => round((isset($value['price']) ? $value['price'] : 0)),
                        'num'    => (isset($value['num']) ? $value['num'] : 0)
                    ];
                }
            }
            return result('', 1, $result);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 业务员排行（区域）
     * @param Request $request
     *                type:时间类型
     *                site_id:省id
     * @return false|string
     */
    public function managerAreaRank(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $data = RecycleOrder::query()
                ->selectRaw('sum(weight) as weight,sum(price) as price,count(id) as num,manager_id')
                ->when($begin, function ($query) use ($begin) {
                    $query->where('created_at', '>', strtotime($begin));
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->where('status', 288)
                ->groupBy('manager_id')
                ->orderBy('weight', 'desc')
                ->take(10)
                ->get()
                ->toArray();

            $result = array();
            $managers = DB::connection('mysql_kd')->table('agent_managers')
                ->whereIn('id', array_column($data, 'manager_id'))
                ->pluck('name', 'id')
                ->toArray();
            foreach ($data as $datum) {
                $result[] = [
                    'name'   => Arr::get($managers, $datum['manager_id']),
                    'weight' => round($datum['weight'] / 1000),
                    'price'  => round($datum['price']),
                    'num'    => $datum['num']
                ];
            }
            return result('', 1, $result);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 回收电池—库存区域分布
     * @param Request $request
     *                site_id:省id
     * @return false|string
     */
    public function stockAreaRank(Request $request)
    {
        try {
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;

            $data = $data = DepotRecycle::query()
                ->selectRaw('sum(sku/1000) as value,site_id')
                ->whereNotIn('site_id', [4, 11])
                ->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->groupBy('site_id')
                ->pluck('value', 'site_id')
                ->toArray();
            $result = array();
            $sites = Site::sites(false);
            foreach ($data as $id => $datum) {
                $result[] = [
                    'name'  => Arr::get($sites, $id, ''),
                    'value' => $datum
                ];
            }

            return result('', 1, $result);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 回收电池—仓库回收库存排名
     * @param Request $request
     *                site_id:省id
     * @return false|string
     */
    public function stocksRank(Request $request)
    {
        try {
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;

            $data = $data = DepotRecycle::query()
                ->selectRaw('sum(sku/1000) as value,depot_id')
                ->whereNotIn('site_id', [4, 11])
                ->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->groupBy('depot_id')
                ->orderBy('value','desc')
                ->take(10)
                ->pluck('value', 'depot_id')
                ->toArray();
            $result = array();
            $depots = Depot::query()->pluck('name','id')->toArray();
            foreach ($data as $id => $datum) {
                $result[] = [
                    'name'  => Arr::get($depots, $id, ''),
                    'value' => $datum
                ];
            }

            return result('', 1, $result);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 回收电池—卖出趋势
     * @param Request $request
     *                site_id:省id
     * @return false|string
     */
    public function sendTrend(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $format = $request->get('type', 1) <= 2 ? 'FROM_UNIXTIME(created_at,"%Y-%m-%d")' : 'FROM_UNIXTIME(created_at,"%Y-%m")';
            $data = DepotRecycleSend::query()
                ->selectRaw("sum(total_weight/1000) as value,$format as date")
                ->when($begin, function ($query) use ($begin) {
                    $query->where('created_at', '>', strtotime($begin));
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->where('status','>=', 548)
                ->groupBy("date")
                ->orderBy('date')->get()->toArray();
            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 回收电池—卖出区域分布
     * @param Request $request
     *                site_id:省id
     * @return false|string
     */
    public function sendArea(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $data = DepotRecycleSend::query()
                ->selectRaw("sum(total_weight/1000) as value,site_id")
                ->when($begin, function ($query) use ($begin) {
                    $query->where('created_at', '>', strtotime($begin));
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->where('status','>=', 548)
                ->groupBy("site_id")
                ->orderBy('value','desc')
                ->pluck('value', 'site_id')
                ->toArray();
            $result = array();
            $sites = Site::sites(false);
            foreach ($data as $id => $datum) {
                $result[] = [
                    'name'  => Arr::get($sites, $id, ''),
                    'value' => $datum
                ];
            }
            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }
}
