<?php

namespace Modules\Manage\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Lease\Models\BlAppDown;
use Modules\Lease\Models\BlLeaseContract;
use Modules\Lease\Models\BlService;
use Modules\Lease\Models\BlServiceApply;
use Modules\Lease\Models\BlUser;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Service\ServiceStock;

class BasicDataController extends Controller
{
    //基础指标
    public function basic(Request $request)
    {
        try {
            $begin = $this->timeBegin($request->get('type', 1));
            $downNum = $this->downNum($begin);
            $provinceId = $request->manager->agent_id;
            $provinceId = !$provinceId ? $request->get('province_id', 0) : $provinceId;
            $serviceNum = $this->serviceNum($begin, $provinceId);
            $data = [
                [
                    'name'  => '新增用户',
                    'value' => $this->userNum($begin, $provinceId)
                ],
                [
                    'name'  => '用户下载',
                    'value' => Arr::get($downNum, "0.num", 0)
                ],
                [
                    'name'  => '新增网点',
                    'value' => $serviceNum && $serviceNum->audited_num ? $serviceNum->audited_num : 0
                ],
                [
                    'name'  => '网点申请',
                    'value' => $this->serviceApplyNum($begin, $provinceId)
                ],
                [
                    'name'  => '网点录入',
                    'value' => $serviceNum ? $serviceNum->num : 0
                ],
                [
                    'name'  => '网点下载',
                    'value' => Arr::get($downNum, "0.num", 0)
                ]
            ];

            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //新增租点用户量
    protected function userNum($begin, $provinceId = null)
    {
        return BlUser::query()->when($begin, function ($query) use ($begin) {
            $query->where('created_at', '>', $begin);
        })->when($provinceId, function ($query) use ($provinceId) {
            $query->where('province_id', $provinceId);
        })->count();
    }

    //新增下载量
    protected function downNum($begin)
    {
        return BlAppDown::query()->selectRaw('count(id) as num,app_type')
            ->when($begin, function ($query) use ($begin) {
                $query->where('created_at', '>', strtotime($begin));
            })->groupBy('app_type')->orderBy('app_type')->get()->toArray();
    }

    //新增网点用户量
    protected function serviceNum($begin, $provinceId = null)
    {
        return BlService::query()
            ->selectRaw("sum(case when audited_at is null THEN 0 ELSE 1 END) as audited_num,count(id) as num")
            ->when($begin, function ($query) use ($begin) {
                $query->where('created_at', '>', $begin);
            })->when($provinceId, function ($query) use ($provinceId) {
                $agents = leaseAgentCache();
                $provinceName = Arr::get($agents, "$provinceId.province_name");
                if ($provinceName) $query->where('province_name', $provinceName);
            })->first();
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
     * 电池租赁型号
     * @param Request $request
     *                type:时间类型
     *                province_id:省id
     * @return false|string
     */
    public function batteryPie(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->agent_id;
            $provinceId = !$provinceId ? $request->get('province_id', 0) : $provinceId;
            $data = BlLeaseContract::query()
                ->selectRaw('model_name as name,count(id) as value')
                ->when($begin, function ($query) use ($begin) {
                    $query->where('created_at', '>', $begin);
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('province_id', $provinceId);
                })
                ->whereNotIn('status', LeaseContract::STATUS_INVALID_ARR)
                ->groupBy('model_name')
                ->orderBy('value', 'desc')->pluck('value', 'name')->toArray();
            //只展示排名前show位
            $showNum = $request->get('show', 5);
            $show = array_slice(array_keys($data), 0, $showNum);
            $result = array();
            $other = 0;
            foreach ($data as $name => $value) {
                if (in_array($name, $show)) {
                    $result[] = [
                        'name'  => $name,
                        'value' => $value
                    ];
                } else {
                    $other += $value;
                }
            }
            $result[] = [
                'name'  => '其它',
                'value' => $other
            ];
            return result('', 1, $result);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 电池租赁趋势
     * @param Request $request
     *                type:时间类型
     *                province_id:省id
     * @return false|string
     */
    public function batteryTrend(Request $request)
    {
        try {
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //当前登陆人可查看区域
            $provinceId = $request->manager->agent_id;
            $provinceId = !$provinceId ? $request->get('province_id', 0) : $provinceId;
            $data = LeaseContract::query()
                ->selectRaw('created_date as name,count(id) as num,sum(rental_all) as amount')
                ->when($begin, function ($query) use ($begin) {
                    $query->where('created_at', '>', $begin);
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('province_id', $provinceId);
                })
                ->whereNotIn('status', LeaseContract::STATUS_INVALID_ARR)
                ->groupBy('created_date')->get()->toArray();
            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 租赁电池库存
     * @param Request $request
     *                province_id:省id
     *                type:1 获取全新备用 2 获取废旧
     * @return false|string
     */
    public function serviceStock(Request $request)
    {
        try {
            //当前登陆人可查看区域
            $provinceId = $request->manager->agent_id;
            $provinceId = !$provinceId ? $request->get('province_id', 0) : $provinceId;
            //根据查看权限判断分组字段
            $groupByColumn = $provinceId ? 'city_id' : 'province_id';
            //判断获取全新备用电池还是废旧电池数据
            $type = $request->get('type', 1);
            $sql = $type == 1 ? "sum(IF(lease_type = 1,sku,0)) as new,sum(IF(lease_type = 2,sku,0)) as standby"
                : "sum(IF(lease_type = 0,sku,0)) as old";
            $data = ServiceStock::query()
                ->selectRaw("$groupByColumn,$sql")
                ->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('province_id', $provinceId);
                })
                ->where($groupByColumn, '>', 0)
                ->groupBy($groupByColumn)->get()->toArray();
            //解析地区名
            $agents = leaseAgentCache(true);
            $name = $provinceId ? 'city_name' : 'province_name';
            foreach ($data as &$datum) {
                $datum['name'] = Arr::get($agents, $datum[$groupByColumn] . ".$name", '未知区域');
            }
            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }
}
