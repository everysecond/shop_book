<?php

function layAsset($path = '', $secure = null)
{
    return asset('resource/lib/layuiadmin/' . ltrim($path, '/'), $secure);
}

function removeNullValue($data)
{
    return array_filter($data, function ($value) {
        if (!is_null($value)) {
            if (is_array($value) && !empty($value)) {
                return removeNullValue($value);
            } else {
                return true;
            }
        }
    });
}

function isMobile($string)
{
    return !!preg_match('/^1[3|4|5|6|7|8|9]\d{9}$/', $string);
}

function i5Search($name = null)
{
    static $searchData;
    if (is_null($searchData)) {
        $searchData = [];
        $search     = request('search');
        if (stripos($search, ':')) {
            $fields = explode(';', $search);

            foreach ($fields as $row) {
                try {
                    list($field, $value) = explode(':', $row);
                    $searchData[$field] = $value;
                } catch (\Exception $e) {
                    //Surround offset error
                }
            }
        }
    }

    if (!is_null($name)) {
        return $searchData[$name] ?? null;
    } else {
        return $searchData;
    }
}

/**
 * @param $list
 * @param string $pk
 * @param string $pid
 * @param string $child
 * @param int $root
 * @return array
 */
function listToTree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent           = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * @param $list
 * @param string $pk
 * @param string $value
 * @param string $pid
 * @param string $child
 * @return array
 */
function listToOptions($list, $pk = 'id', $value = 'name', $pid = 'pid', $child = '_child')
{
    $tree    = listToTree($list, $pk, $pid, $child);
    $options = [];
    foreach ($tree as $key => $val) {
        if (!empty($val[$child])) {
            $items = [];
            foreach ($val[$child] as $k => $v) {
                $items[$v[$pk]] = $v[$value];
            }
            $options[$val[$value]] = $items;
        } else {
            $options[$val[$pk]] = $val[$value];
        }
    }
    return $options;
}

function routeToUrl($name)
{
    return $name && Route::has($name) ? route($name) : 'javascript:;';
}

//所有租点区域分级信息 ***数据量较大，只适用于定时任务
function leaseAgentCache($cache = false)
{
    if (!$cache) {
        Cache::forget('agentsCache');
    }

    $cacheKey = 'agentsCache';
    if (Cache::has($cacheKey)) {
        return Cache::get($cacheKey);
    } else {
        $agents = \Modules\Lease\Models\BlAgent::query()->select('id', 'pid', 'name')->get()->toArray();
        foreach ($agents as &$agent) {
            $agent['name'] = str_replace(['省', '市', '区', '县', '城'], '', $agent['name']);
        }

        $trees = listToTree($agents, 'id', 'pid', 'child');

        $defaultAgentArr = [
            'province_id'   => 0,
            'province_name' => '',
            'city_id'       => 0,
            'city_name'     => '',
            'county_id'     => 0,
            'county_name'   => '',
            'address'       => ''
        ];
        $result          = array();
        foreach ($trees as $province) {
            $result[$province['id']]                  = $defaultAgentArr;
            $result[$province['id']]['province_id']   = $province['id'];
            $result[$province['id']]['province_name'] = $province['name'];
            $citys                                    = \Illuminate\Support\Arr::get($province, 'child');
            if (!$citys) continue;
            foreach ($citys as $city) {
                $result[$city['id']]                  = $defaultAgentArr;
                $result[$city['id']]['province_id']   = $province['id'];
                $result[$city['id']]['province_name'] = $province['name'];
                $result[$city['id']]['city_id']       = $city['id'];
                $result[$city['id']]['city_name']     = $city['name'];
                $countys                              = \Illuminate\Support\Arr::get($city, 'child');
                if (!$countys) continue;
                foreach ($countys as $county) {
                    $result[$county['id']]                  = $defaultAgentArr;
                    $result[$county['id']]['province_id']   = $province['id'];
                    $result[$county['id']]['province_name'] = $province['name'];
                    $result[$county['id']]['city_id']       = $city['id'];
                    $result[$county['id']]['city_name']     = $city['name'];
                    $result[$county['id']]['county_id']     = $county['id'];
                    $result[$county['id']]['county_name']   = $county['name'];
                }
            }
        }
        Cache::put($cacheKey, $result, config('cache.cache_expired', 3600 * 1));
        return $result;
    }
}

//agent_id及province_id数组
function agentIdAndProvinceId()
{
    $agents = \Modules\Lease\Models\BlAgent::query()->pluck('pid', 'id')->toArray();
    $arr    = [];
    foreach ($agents as $id => $pid) {
        if ($top = getTopId($id, $agents)) {
            $arr[$id] = $top;
        }
    }
    return $arr;
}

function getTopId($agentId, $agentArr, $count = 0)
{
    $count++;
    //防数组死循环
    if ($count > 10) {
        return false;
    }
    if (isset($agentArr[$agentId])) {
        if ($agentArr[$agentId] == 0) {
            return $agentId;
        } else {
            return getTopId($agentArr[$agentId], $agentArr);
        }
    }
    return false;
}

//所有内部用户 包括已软删除的
function allUsersArr()
{
    $data = \App\Models\Manager::query()->select('id', 'name', 'mobile')->withTrashed()->get()->toArray();
    $arr  = [];
    foreach ($data as $datum) {
        $arr[$datum['id']] = $datum;
    }

    return $arr;
//
//
//    $cacheKey = 'all_users';
//    if (Cache::has($cacheKey)) {
//        return Cache::get($cacheKey);
//    } else {
//        $data = \App\Models\Manager::query()->select('id', 'name', 'mobile')->get()->toArray();
//        $arr = [];
//        foreach ($data as $datum) {
//            $arr[$datum['id']] = $datum;
//        }
//        Cache::put($cacheKey, $arr, config('cache.cache_expired', 3600 * 8));
//        return $data;
//    }
}

//所有租点区域
function allAgentsArr()
{
    $cacheKey = 'all_agents';
    if (Cache::has($cacheKey)) {
        return Cache::get($cacheKey);
    } else {
        $data = \Modules\Lease\Models\BlAgent::query()->pluck('name', 'id')->toArray();
        Cache::put($cacheKey, $data, config('cache.cache_expired', 3600 * 8));
        return $data;
    }
}

function getUser()
{
    return \Illuminate\Support\Facades\Auth::user();
}

function getUserId()
{
    return getUser()->id;
}

function getUserName()
{
    return getUser()->name;
}

//当前生效字典
function dictArr($type)
{
    return \Modules\Manage\Models\Crm\SysDictionary::query()
        ->where("dict_type", $type)
        ->pluck('means', 'code')
        ->toArray();
}

//包括已被软删除字典
function dictArrAll($type)
{
    return \Modules\Manage\Models\Crm\SysDictionary::query()
        ->where("dict_type", $type)
        ->withTrashed()
        ->pluck('means', 'code')
        ->toArray();
}

//字典查找
function dictMean($type, $code)
{
    return dictArrAll($type)[$code] ?? "";
}

function MB2Bytes($mb)
{
    return intval($mb) * 1048576;
}

function retArr($msg = '', $data = [], $code = \Illuminate\Http\Response::HTTP_OK)
{
    $data = array_merge($data, ['msg' => $msg, 'code' => $code]);
    return new \Illuminate\Http\Response($data, \Illuminate\Http\Response::HTTP_OK);
}


function modifyEnv(array $data)
{
    if (!getUser()->isSuper()) return false;
    $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';

    $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));

    $contentArray->transform(function ($item) use ($data) {
        foreach ($data as $key => $value) {
            if (str_contains($item, $key)) {
                return $key . '=' . $value;
            }
        }

        return $item;
    });

    $content = implode($contentArray->toArray(), "\n");

    \File::put($envPath, $content);
}

//过滤微信emoji表情
function filterEmoji($text, $replaceTo = '?')
{
    $clean_text = "";
    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text     = preg_replace($regexEmoticons, $replaceTo, $text);
    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text   = preg_replace($regexSymbols, $replaceTo, $clean_text);
    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text     = preg_replace($regexTransport, $replaceTo, $clean_text);
    // Match Miscellaneous Symbols
    $regexMisc  = '/[\x{2600}-\x{26FF}]/u';
    $clean_text = preg_replace($regexMisc, $replaceTo, $clean_text);
    // Match Dingbats
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
    $clean_text    = preg_replace($regexDingbats, $replaceTo, $clean_text);
    return $clean_text;
}

function timeTypeDate($request)
{
    $defaultDay = [
        "begin" => date("Y-m-d", strtotime("-6 days")),
        "end"   => date("Y-m-d") . ' 23:59:59'
    ];
    if ($request->days && $request->days != -1) {
        $days                = $request->days;
        $defaultDay["begin"] = date("Y-m-d", strtotime("-$days day"));
    } else if ($request->days && $request->days == -1 && $request->dateRange) {
        $days       = explode(" - ", $request->dateRange);
        $defaultDay = [
            "begin" => $days[0],
            "end"   => $days[1]
        ];
    }
    return $defaultDay;
}

//获取不同电池类型库存所有电池型号
function batteryStockTitle($type)
{
    return \Modules\Lease\Models\BlServiceStock::query()
        ->where('battery_type', $type)
        ->groupBy('model_name')->pluck('model_name')->toArray();
}

function array_values_int(array $array, int $int = 0)
{
    $arr = [];
    foreach ($array as $key => $value) {
        $arr[$key] = $int;
    }
    return $arr;
}

function array_round(array $array, int $precision = 0)
{
    $arr = [];
    foreach ($array as $key => $value) {
        $arr[$key] = round($value, $precision);
    }
    return $arr;
}

//所有租点区域分级信息 ***数据量较大，只适用于定时任务
function agentsTree($cache = false)
{
    if (!$cache) {
        Cache::forget('agentsTree');
    }

    $cacheKey = 'agentsTree';
    if (Cache::has($cacheKey)) {
        return Cache::get($cacheKey);
    } else {
        $agents = \Modules\Lease\Models\BlAgent::query()->select('id', 'pid', 'name')->get()->toArray();

        foreach ($agents as &$agent) {
            $agent['name'] = str_replace(['省', '市', '区', '县', '城'], '', $agent['name']);
        }

        $trees = listToTree($agents, 'id', 'pid', 'child');
        Cache::put($cacheKey, $trees, config('cache.cache_expired', 3600 * 1));
        return $trees;
    }
}

function dateFormat($date, $format = 'Y-m-d H:i:s')
{
    return date($format, strtotime($date));
}

function siteCache()
{
    $cacheKey = 'kdSitesCache';
    if (Cache::has($cacheKey)) {
        return Cache::get($cacheKey);
    } else {
        $sites = \Modules\Kood\Models\Site::query()->pluck('name', 'id')->toArray();
        foreach ($sites as $id => &$datum) {
            $datum = str_replace('区', '', $datum);
        }
        Cache::put($cacheKey, $sites, config('cache.cache_expired', 3600 * 1));
        return $sites;
    }
}

//快点自建区域树形arr
function kdAgentTree()
{
    $cacheKey = 'kdAgentsCache';
    if (Cache::has($cacheKey)) {
        return Cache::get($cacheKey);
    } else {
        $agents = \Modules\Kood\Models\Agent::query()
            ->selectRaw('if( pid = 0,site_id+100000,pid) as pid,id,name')
            ->get()->toArray();
        $sites  = \Modules\Kood\Models\Site::query()
            ->selectRaw('id+100000 as id,0 as pid,name')
            ->get()->toArray();
        $trees  = listToTree(array_merge($agents, $sites), 'id', 'pid', 'children');
        Cache::put($cacheKey, $trees, config('cache.cache_expired', 3600 * 1));
        return $trees;
    }
}

//按区域划分
function kdAgentArr()
{
    $result = array();
    foreach (kdAgentTree() as $site) {
        $cityArr      = array();
        $cityChildArr = array();
        if ($cities = \Illuminate\Support\Arr::get($site, 'children')) {
            foreach ($cities as $city) {
                $cityArr[$city['id']]      = $city['name'];
                $cityChildArr[$city['id']] = array_merge(kdChildIds($city), [$city['id']]);
            }
            $result[$site['id']] = [
                'name'           => $site['name'],
                'city_arr'       => $cityArr,
                'city_child_arr' => $cityChildArr
            ];
        }
    }
    return $result;
}

function kdChildIds($city, &$result = array())
{
    if ($items = \Illuminate\Support\Arr::get($city, 'children')) {
        foreach ($items as $item) {
            $result[] = $item['id'];
            $result   = kdChildIds($item, $result);
        }
    }
    return $result;
}

function logType($relationType,$leaseType)
{
    if($relationType == "App\Models\LeaseServiceModel"){
        if($leaseType == 0){
            return 0;
        } elseif ($leaseType == 1) {
            return 1;
        } elseif ($leaseType == 4) {
            return 2;
        }
    } elseif ($relationType == "App\Models\LeaseExchangeModel"){
        if($leaseType == 2){
            return 4;
        } elseif ($leaseType == 4) {
            return 3;
        }
    } elseif ($relationType == "App\Models\ServiceRetrieveModel"){
        if($leaseType == 0){
            return 5;
        } elseif ($leaseType == 1) {
            return 6;
        } elseif ($leaseType == 2) {
            return 7;
        } elseif ($leaseType == 4) {
            return 8;
        }
    } elseif ($relationType == "App\Models\ServiceSupplyModel"){
        if($leaseType == 1){
            return 9;
        } elseif ($leaseType == 2) {
            return 10;
        }
    }
    return 0;
}

function logisticsLogType($relationType,$leaseType)
{
    if($relationType == "App\Models\DepotDeliverModel"){
        if($leaseType == 1){
            return 0;
        } elseif ($leaseType == 2) {
            return 1;
        }
    } elseif ($relationType == "App\Models\DepotRefundModel"){
        if($leaseType == 1){
            return 2;
        } elseif ($leaseType == 2) {
            return 3;
        }
    } elseif ($relationType == "App\Models\DepotRetrieveModel"){
        if($leaseType == 0){
            return 4;
        } elseif ($leaseType == 1) {
            return 5;
        } elseif ($leaseType == 2) {
            return 6;
        } elseif ($leaseType == 4) {
            return 7;
        }
    } elseif ($relationType == "App\Models\ServiceRetrieveModel"){
        if($leaseType == 0){
            return 8;
        } elseif ($leaseType == 1) {
            return 9;
        } elseif ($leaseType == 2) {
            return 10;
        } elseif ($leaseType == 4) {
            return 11;
        }
    } elseif ($relationType == "App\Models\ServiceSupplyModel"){
        if($leaseType == 1){
            return 12;
        } elseif ($leaseType == 2) {
            return 13;
        }
    }
    return 0;
}