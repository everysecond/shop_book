<?php

namespace Modules\Manage\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Kood\Models\Lookup;
use Modules\Kood\Models\RecycleOrder;
use Modules\Kood\Models\SaleOrder;
use Modules\Kood\Models\ServiceApply;
use Modules\Kood\Models\Site;
use Modules\Kood\Models\User;
use Modules\Lease\Models\BlServiceApply;
use Modules\Manage\Http\Controllers\Controller;

class KdHomeController extends Controller
{
    //站点list
    public function siteMenu(Request $request)
    {
        $all = Site::sites(false);
        $all[0] = "全部区域";
        $agent_id = $request->manager->site_id;
        if (!$agent_id) {
            return result('', 1, $all);
        } else {
            $data = [];
            $data[$agent_id] = $all[$agent_id];
            return result('', 1, $data);
        }


    }

    //基础指标
    public function simple(Request $request)
    {
        try {
            $begin = $this->timeBegin($request->get('type', 1));
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $sale = $this->saleOrderData($begin, $provinceId);
            $recycle = $this->recycleOrderData($begin, $provinceId);
            $user = $this->userNum($begin, $provinceId);
            $data = [
                [
                    'name'  => '销售电池数（组）',
                    'value' => $sale && $sale->battery_num ? $sale->battery_num : 0
                ],
                [
                    'name'  => '销售订单数（个）',
                    'value' => $sale && $sale->order_num ? $sale->order_num : 0
                ],
                [
                    'name'  => '销售金额（元）',
                    'value' => $sale && $sale->total_amount ? $sale->total_amount : 0
                ],
                [
                    'name'  => '电池回收量（吨）',
                    'value' => floor(($recycle && $recycle->weight ? $recycle->weight : 0) / 1000)
                ],
                [
                    'name'  => '回收订单数（个）',
                    'value' => $recycle && $recycle->order_num ? $recycle->order_num : 0
                ],
                [
                    'name'  => '回收支付额（元）',
                    'value' => $recycle && $recycle->total_amount ? $recycle->total_amount : 0
                ],
                [
                    'name'  => '售后电池数（组）',
                    'value' => $this->serviceApply($begin, $provinceId) ?? 0
                ],
                [
                    'name'  => '新增商户数（个）',
                    'value' => $user && $user->new ? $user->new : 0
                ],
                [
                    'name'  => '商户注册数（个）',
                    'value' => $user && $user->register ? $user->register : 0
                ]
            ];
            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //售后订单电池数
    protected function serviceApply($begin, $provinceId = null)
    {
        return ServiceApply::query()
            ->when($begin, function ($query) use ($begin) {
                $query->where('created_at', '>', strtotime($begin));
            })->when($provinceId, function ($query) use ($provinceId) {
                $query->where('site_id', $provinceId);
            })
            ->where('progress', Lookup::getCodeByCall('service.apply', 'completed'))
            ->whereNull('deleted_at')
            ->sum('amount');
    }

    //商家数
    protected function userNum($begin, $provinceId = null)
    {
        return User::query()
            ->selectRaw('sum(if(authentic_status=2,1,0)) as new,count(id) as register')
            ->when($begin, function ($query) use ($begin) {
                $query->where('register_at', '>', strtotime($begin));
            })->when($provinceId, function ($query) use ($provinceId) {
                $query->where('site_id', $provinceId);
            })
            ->whereNull('deleted_at')
            ->first();
    }

    //销售订单数据
    protected function saleOrderData($begin, $provinceId = null)
    {
        return SaleOrder::query()
            ->selectRaw("sum(total_amount) as total_amount,count(id) as order_num,sum(num) as battery_num")
            ->when($begin, function ($query) use ($begin) {
                $query->where('created_at', '>', strtotime($begin));
            })->when($provinceId, function ($query) use ($provinceId) {
                $query->where('site_id', $provinceId);
            })->whereIn('status', [221, 232, 242, 252, 281])
            ->first();
    }

    //回收订单数据
    protected function recycleOrderData($begin, $provinceId = null)
    {
        return RecycleOrder::query()
            ->selectRaw("sum(total_amount) as total_amount,count(id) as order_num,sum(weight) as weight")
            ->when($begin, function ($query) use ($begin) {
                $query->where('created_at', '>', strtotime($begin));
            })->when($provinceId, function ($query) use ($provinceId) {
                $query->where('site_id', $provinceId);
            })->where('status', 288)
            ->first();
    }

    //网点申请量
    protected function serviceApplyNum($begin, $provinceId = null)
    {
        return BlServiceApply::query()
            ->when($begin, function ($query) use ($begin) {
                $query->where('created_at', '>', $begin);
            })->when($provinceId, function ($query) use ($provinceId) {
                $agents = leaseAgentCache(true);
                $provinceName = Arr::get($agents, "$provinceId.province_name");
                if ($provinceName) $query->where('province_name', 'like', "%$provinceName%");
            })->count();
    }

    /**
     * 电池回收排行（区域）
     * @param Request $request
     *                type:时间类型
     *                site_id:省id
     * @return false|string
     */
    public function batteryRecycle(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $groupByColumn = $provinceId ? 'agent_id' : 'site_id';
            $data = RecycleOrder::query()
                ->selectRaw('sum(weight) as weight,agent_id,site_id')
                ->when($begin, function ($query) use ($begin) {
                    $query->where('created_at', '>', strtotime($begin));
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->where('status', 288)
                ->groupBy($groupByColumn)
                ->orderBy('weight', 'desc')->pluck('weight', $groupByColumn)->toArray();

            $result = array();
            if ($groupByColumn == 'site_id') {
                $sites = siteCache();
                foreach ($data as $id => $datum) {
                    $result[] = [
                        'name'  => Arr::get($sites, $id),
                        'value' => round($datum / 1000)
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
                        'value' => round($value / 1000)
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
    public function managersRank(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $data = RecycleOrder::query()
                ->selectRaw('sum(weight) as value,manager_id')
                ->when($begin, function ($query) use ($begin) {
                    $query->where('created_at', '>', strtotime($begin));
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->where('status', 288)
                ->groupBy('manager_id')
                ->orderBy('value', 'desc')
                ->take(10)
                ->pluck('value', 'manager_id')->toArray();

            $result = array();
            $managers = DB::connection('mysql_kd')->table('agent_managers')
                ->whereIn('id', array_keys($data))
                ->pluck('name', 'id')
                ->toArray();
            foreach ($data as $id => $datum) {
                $result[] = [
                    'name'  => Arr::get($managers, $id),
                    'value' => round($datum / 1000)
                ];
            }
            return result('', 1, $result);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 电池回收趋势
     * @param Request $request
     *                type:时间类型
     *                site_id:省id
     * @return false|string
     */
    public function recycleTrend(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $format = $request->get('type', 1) <= 2 ? 'FROM_UNIXTIME(created_at,"%Y-%m-%d")' : 'FROM_UNIXTIME(created_at,"%Y-%m")';
            $data = RecycleOrder::query()
                ->selectRaw("sum(price) as money,count(id) as num,sum(weight) as weight,$format as date")
                ->when($begin, function ($query) use ($begin) {
                    $query->where('created_at', '>', strtotime($begin));
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->where('status', 288)
                ->groupBy("date")
                ->orderBy('date')->get()->toArray();
            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 回收电池型号占比
     * @param Request $request
     *                type:时间类型
     *                site_id:省id
     * @return false|string
     */
    public function recyclePie(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $data = RecycleOrder::query()->from('recycle_orders as a')
                ->leftJoin('recycle_order_records as b', 'a.id', '=', 'b.order_id')
                ->selectRaw('sum(kd_b.weight) as weights,kd_b.category_id')
                ->when($begin, function ($query) use ($begin) {
                    $query->where('a.created_at', '>', strtotime($begin));
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('a.site_id', $provinceId);
                })
                ->where('a.status', 288)
                ->whereNull('b.deleted_at')
                ->groupBy('b.category_id')
                ->orderBy('weights', 'desc')->get()->toArray();
            $categoryArr = DB::connection('mysql_kd')->table('battery_categories')->pluck('alias', 'id')->toArray();
            $result = array();
            $other = 0;
            $show = $request->get('show', 3) - 1;
            foreach ($data as $k => $datum) {
                if ($k <= $show) {
                    $result[] = [
                        "name"  => Arr::get($categoryArr, $datum['category_id']),
                        "value" => $datum["weights"] / 1000
                    ];
                } else {
                    $other += $datum["weights"] / 1000;
                }
            }
            $result[] = [
                "name"  => "其它",
                "value" => $other
            ];
            return result('', 1, $result);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }
}
