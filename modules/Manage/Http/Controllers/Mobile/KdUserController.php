<?php

namespace Modules\Manage\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Kood\Models\User;
use Modules\Manage\Http\Controllers\Controller;

class KdUserController extends Controller
{
    /**
     * 商务增长趋势
     * @param Request $request
     *                type:时间类型
     *                site_id:省id
     * @return false|string
     */
    public function userTrend(Request $request)
    {
        try {
            $begin = $this->timeSelect($request->get('type', 1));
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $data = User::query()
                ->selectRaw('FROM_UNIXTIME(register_at,"%Y-%m-%d") as name,count(id) as value')
                ->when($begin, function ($query) use ($begin) {
                    $query->where('register_at', '>', strtotime($begin));
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->where("authentic_status", "2")
                ->whereNull('deleted_at')
                ->groupBy('name')
                ->get()
                ->toArray();
            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 待审核商家
     * @param Request $request
     * @return false|string
     */
    public function toAuth(Request $request)
    {
        try {
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $data = User::query()
                ->selectRaw('site_id,count(id) as value')
                ->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->where("authentic_status", "1")
                ->whereNull('deleted_at')
                ->groupBy('site_id')
                ->get()
                ->toArray();
            return result('', 1, $this->transformSite($data));
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 商家分布
     * @param Request $request
     * @return false|string
     */
    public function userArea(Request $request)
    {
        try {
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $data = User::query()
                ->selectRaw('site_id,count(id) as value')
                ->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->where("authentic_status", "2")
                ->whereNull('deleted_at')
                ->groupBy('site_id')
                ->get()
                ->toArray();
            return result('', 1, $this->transformSite($data));
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 实名认证
     * @param Request $request
     *                site_id:省id
     * @return false|string
     */
    public function authenticated(Request $request)
    {
        try {
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $data = User::query()
                ->selectRaw('if(authentic = 1,"已认证","未认证") as name,count(id) as value')
                ->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->whereNull('deleted_at')
                ->groupBy('authentic')
                ->get()
                ->toArray();
            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 余额区间分布
     * @param Request $request
     *                site_id:省id
     * @return false|string
     */
    public function balanceRange(Request $request)
    {
        try {
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $sql = "elt(interval(balance,0,10,100,10000,1000000),"
                . "'0-10','10-100','100-1万','1万-1百万','1百万以上')";
            $data = User::query()
                ->selectRaw("$sql as name,count(id) as value")
                ->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->whereNull('deleted_at')
                ->where("authentic_status", "2")
                ->groupBy('name')
                ->get()
                ->toArray();
            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 各区域分布
     * @param Request $request
     *                site_id:省id
     * @return false|string
     */
    public function balanceArea(Request $request)
    {
        try {
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $groupByColumn = $provinceId ? 'agent_id' : 'site_id';
            $data = User::query()
                ->selectRaw("sum(balance) as value,$groupByColumn as name")
                ->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->whereNull('deleted_at')
                ->where("authentic_status", "2")
                ->groupBy($groupByColumn)
                ->pluck('value', 'name')->toArray();
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
                    $idArr = Arr::get($agents, "city_child_arr.$id");
                    foreach ($data as $agentId => $datum) {
                        if (in_array($agentId, $idArr)) {
                            $arr[$name] = isset($arr[$name]) ? $arr[$name] + $datum : $datum;
                        }
                    }
                }
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
     * 预付款区间分布
     * @param Request $request
     *                site_id:省id
     * @return false|string
     */
    public function advanceRange(Request $request)
    {
        try {
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $sql = "elt(interval(advance_balance,0,10,100,1000,10000),"
                . "'0-10','10-100','100-1000','1000-1万','1万以上')";
            $data = User::query()
                ->selectRaw("$sql as name,count(id) as value")
                ->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->whereNull('deleted_at')
                ->where("authentic_status", "2")
                ->groupBy('name')
                ->get()
                ->toArray();
            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 预付款区域分布
     * @param Request $request
     *                site_id:省id
     * @return false|string
     */
    public function advanceArea(Request $request)
    {
        try {
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $groupByColumn = $provinceId ? 'agent_id' : 'site_id';
            $data = User::query()
                ->selectRaw("sum(advance_balance) as value,$groupByColumn as name")
                ->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->whereNull('deleted_at')
                ->where("authentic_status", "2")
                ->groupBy($groupByColumn)
                ->pluck('value', 'name')->toArray();
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
                    $idArr = Arr::get($agents, "city_child_arr.$id");
                    foreach ($data as $agentId => $datum) {
                        if (in_array($agentId, $idArr)) {
                            $arr[$name] = isset($arr[$name]) ? $arr[$name] + $datum : $datum;
                        }
                    }
                }
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
     * 专用金区间分布
     * @param Request $request
     *                site_id:省id
     * @return false|string
     */
    public function specialRange(Request $request)
    {
        try {
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $sql = "elt(interval(special_balance,0,10,100,1000,10000),"
                . "'0-10','10-100','100-1000','1000-1万','1万以上')";
            $data = User::query()
                ->selectRaw("$sql as name,count(id) as value")
                ->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->whereNull('deleted_at')
                ->where("authentic_status", "2")
                ->groupBy('name')
                ->get()
                ->toArray();
            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 专用金区域分布
     * @param Request $request
     *                site_id:省id
     * @return false|string
     */
    public function specialArea(Request $request)
    {
        try {
            //当前登陆人可查看区域
            $provinceId = $request->manager->site_id;
            $provinceId = !$provinceId ? $request->get('site_id', 0) : $provinceId;
            $groupByColumn = $provinceId ? 'agent_id' : 'site_id';
            $data = User::query()
                ->selectRaw("sum(special_balance) as value,$groupByColumn as name")
                ->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('site_id', $provinceId);
                })
                ->whereNull('deleted_at')
                ->where("authentic_status", "2")
                ->groupBy($groupByColumn)
                ->pluck('value', 'name')->toArray();
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
                    $idArr = Arr::get($agents, "city_child_arr.$id");
                    foreach ($data as $agentId => $datum) {
                        if (in_array($agentId, $idArr)) {
                            $arr[$name] = isset($arr[$name]) ? $arr[$name] + $datum : $datum;
                        }
                    }
                }
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


}
