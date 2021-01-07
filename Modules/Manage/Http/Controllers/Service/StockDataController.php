<?php

namespace Modules\Manage\Http\Controllers\Service;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Lease\Models\BlServiceRetrieve;
use Modules\Lease\Models\BlServiceStockLease;
use Modules\Lease\Models\BlServiceStockLog;
use Modules\Lease\Models\BlServiceSupplie;
use Modules\Lease\Models\BlServiceSupplies;
use Modules\Lease\Models\BlServiceSupply;
use Modules\Manage\Models\Service\Stock;

class StockDataController extends Controller
{
    protected $leaseUserModel;
    protected $stock;
    
    public function __construct(Stock $Stock)
    {
        $this->stock = $Stock;
    }
    
    //补货view
    public function stockview()
    {
        $timeType = timeType();
        return view("manage::lease.service.stock.portrayal", compact("timeType"));
    }
    
    //退货 view
    public function cancelsview()
    {
        $timeType = dateType();
        return view("manage::lease.service.stock.cancels_view", compact("timeType"));
    }
    
    //回收 view
    public function retrieveview()
    {
        $timeType = dateType();
        return view("manage::lease.service.stock.retrieve_view", compact("timeType"));
    }
    
    //库存统计 view
    public function statisticsview()
    {
        $timeType = dateType();
        return view("manage::lease.service.stock.statistics_view", compact("timeType"));
    }
    
    
    //补货数据 柱状图
    public function replenishmentData(Request $request)
    {
//        try {
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $agentsMap[0] = '总数';
            
            //判断时间 getProvinceId
            $renewal_date = $request->datetime;
            $time_type = !empty($request->time_type) ? $request->time_type : 1;
            $where = [];
            if ($time_type) {
                $time = selectTimeRange($time_type);
                if ($time) {
                    $where[] = ['created_at', '>=', $time['start_time']];
                    $where[] = ['created_at', '<', $time['end_time']];
                } else {
                    $time_s_d = explode(' - ', $renewal_date);
                    $where[] = ['created_at', '>=', $time_s_d[0]];
                    $where[] = ['created_at', '<', $time_s_d[1]];    //考虑加一天，正好是第二天的零点
                }
            }
            $type = !empty($request->type) ? $request->type : 1;
            if($type == 1){
          
                $areaData = BlServiceSupply::selectRaw("agent_id, count(id) as nums ");
    
            }else{
                
                $areaData = BlServiceSupply::selectRaw("agent_id, SUM(num) as nums ");
    
            }
            $areaData->where($where)->where('status', 1)->groupBy("agent_id");
            $areaData = $areaData->pluck("nums", "agent_id")->toArray();
            $replaceArea = [];
            if (!empty($areaData)) {
                foreach ($areaData as $id => $num) {
                    $getProvinceIdOne = getProvinceId($id);
                    $replaceArea[][$getProvinceIdOne['name']] = $num;
                }
                $getProvinceData = comm_sumarrs($replaceArea);
                
                ksort($agentsMap);
                $total = 0;
                foreach ($agentsMap as $key => $name) {
                    $value = isset($getProvinceData[$name]) ? $getProvinceData[$name] : 0;
                    $per = $value;
                    $arrarea[] = $name;
                    $arrareadata[] = $value;
                    $total += $per;
                }
                $arrareadata[0] = $total;
                ksort($arrareadata);
                $supply = [$arrareadata[0]];
                $total = $arrareadata[0];
                foreach ($arrareadata as $k => $value) {
                    if ($k > 0) {
                        $supply[] = $total - $arrareadata[$k];
                        $total -= $arrareadata[$k];
                     
                    }
                }
                $supply[0] = 0;
                //返回值是一个二维数组 分别放区域和数量
                $returnarea = [
                    'areaname' => $arrarea,
                    'areadata' => $arrareadata,
                    'areadatacum' => $supply,
                ];
                
                return result("", 1, $returnarea);
            } else {
                return result("", -1);
            }
//        } catch (\Exception $exception) {
//            return result("", -1);
//        }
    }
    
    //补货数据 地图
    public function replenishmentArea(Request $request)
    {
        try {
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $provincesArr = getAllProvincesName();
            //判断时间
            $renewal_date = $request->datetime;
            $areaData = BlServiceStockLog::selectRaw("agent_id, count(id) as num ");
            $time_type = !empty($request->time_type) ? $request->time_type : 1;
            if ($time_type) {
                $time = selectTimeRange($time_type);
                if ($time) {
                    $where[] = ['created_at', '>=', $time['start_time']];
                    $where[] = ['created_at', '<', $time['end_time']];
                } else {
                    $time_s_d = explode(' - ', $renewal_date);
                    $where[] = ['created_at', '>=', $time_s_d[0]];
                    $where[] = ['created_at', '<', $time_s_d[1]];    //考虑加一天，正好是第二天的零点
                }
            }
            $areaData->where($where)->groupBy("agent_id");
            $areaData = $areaData->pluck("num", "agent_id")->toArray();
            $max = max($areaData);
            $min = min($areaData);
            if (!empty($areaData)) {
                foreach ($areaData as $id => $num) {
                    $getProvinceIdOne = getProvinceId($id);
                    $getProvinceData[][$getProvinceIdOne['name']] = $num;
                    
                }
                $replaceArea = comm_sumarrs($getProvinceData);
                
                ksort($agentsMap);
                $areaDataFormat = [];
                foreach ($provincesArr as $name) {
                    $value = isset($replaceArea[$name]) ? $replaceArea[$name] : 0;
                    $areaDataFormat[] = [
                        "value" => $value,
                        "name" => $name
                    ];
                }
                $returnData = [
                    "max" => $max,
                    "min" => 0,
                    "areaData" => $areaDataFormat,
                ];
                
                return result("", 1, $returnData);
            } else {
                return result("", -1);
            }
        } catch (\Exception $exception) {
            return result("", -1);
        }
    }
    
    //补货数据 统计列表
    public function replenishmentList(Request $request)
    {
        $list = $this->stock->getLists($request, 1);
        $data = $list['list'];
        $count = $list['count'];
        
        return result("", 0, $data, $count);
    }
    
    
    //退货数据 柱状图
    public function cancelsData(Request $request)
    {
        try {
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $agentsMap[0] = '总数';
            
            //判断时间 getProvinceId
            $renewal_date = $request->datetime;
            $type = !empty($request->type) ? $request->type : 1;
            $time_type = !empty($request->time_type) ? $request->time_type : 1;
            if ($time_type) {
                $time = selectTimeRange($time_type);
                if ($time) {
                    $where[] = ['created_at', '>=', $time['start_time']];
                    $where[] = ['created_at', '<', $time['end_time']];
                } else {
                    $time_s_d = explode(' - ', $renewal_date);
                    $where[] = ['created_at', '>=', $time_s_d[0]];
                    $where[] = ['created_at', '<', $time_s_d[1]];    //考虑加一天，正好是第二天的零点
                }
            }

//        //退货状态为  status 3 ->where('status', 3)
            if($type == 1) {
                $areaData = BlServiceRetrieve::selectRaw("agent_id, count(id) as nums ");
            }else{
                $areaData = BlServiceRetrieve::selectRaw("agent_id, SUM(num) as nums ");
            }
            $areaData->where($where)->where('status', 3)->groupBy("agent_id");
            $areaData = $areaData->pluck("nums", "agent_id")->toArray();
            
            $replaceArea = [];
            if (!empty($areaData)) {
                foreach ($areaData as $id => $num) {
                    $getProvinceIdOne = getProvinceId($id);
                    $replaceArea[][$getProvinceIdOne['name']] = $num;
                }
                $getProvinceData = comm_sumarrs($replaceArea);
                
                ksort($agentsMap);
                $total = 0;
                foreach ($agentsMap as $key => $name) {
                    $value = isset($getProvinceData[$name]) ? $getProvinceData[$name] : 0;
                    $per = $value;
                    $arrarea[] = $name;
                    $arrareadata[] = $value;
                    $total += $per;
                }
                $arrareadata[0] = $total;
                ksort($arrareadata);
                $supply = [$arrareadata[0]];
                $total = $arrareadata[0];
                foreach ($arrareadata as $k => $value) {
                    if ($k > 0) {
                        $supply[$k] = $total - $arrareadata[$k];
                        $total -= $arrareadata[$k];
                    }
                }
                $supply[0]=0;
                //返回值是一个二维数组 分别放区域和数量
                $returnarea = [
                    'areaname' => $arrarea,
                    'areadata' => $arrareadata,
                    'areadatacum' => $supply,
                ];
                
                return result("", 1, $returnarea);
            } else {
                return result("", -1);
            }
        } catch (\Exception $exception) {
            return result("", -1);
        }
    }
    
    //退货数据 地图
    public function cancelsArea(Request $request)
    {
        try {
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $provincesArr = getAllProvincesName();
            //判断时间
            $renewal_date = $request->datetime;
            
            $time_type = !empty($request->time_type) ? $request->time_type : 1;
            $type = !empty($request->type) ? $request->type : 1;
            if ($time_type) {
                $time = selectTimeRange($time_type);
                if ($time) {
                    $where[] = ['created_at', '>=', $time['start_time']];
                    $where[] = ['created_at', '<', $time['end_time']];
                } else {
                    $time_s_d = explode(' - ', $renewal_date);
                    $where[] = ['created_at', '>=', $time_s_d[0]];
                    $where[] = ['created_at', '<', $time_s_d[1]];    //考虑加一天，正好是第二天的零点
                }
            }
            if($type == 1){
                $areaData = BlServiceRetrieve::selectRaw("agent_id, count(id) as num ");
            }else{
                $areaData = BlServiceRetrieve::selectRaw("agent_id, SUM(id) as num ");
            }
            
            $areaData->where($where)->where('status', 3)->groupBy("agent_id");
            $areaData = $areaData->pluck("num", "agent_id")->toArray();
            $max = max($areaData);
            $min = min($areaData);
            
            if (!empty($areaData)) {
                foreach ($areaData as $id => $num) {
                    $getProvinceIdOne = getProvinceId($id);
                    $getProvinceData[][$getProvinceIdOne['name']] = $num;
                    
                }
                
                $replaceArea = comm_sumarrs($getProvinceData);
                
                ksort($agentsMap);
                $areaDataFormat = [];
                foreach ($provincesArr as $name) {
                    $value = isset($replaceArea[$name]) ? $replaceArea[$name] : 0;
                    $areaDataFormat[] = [
                        "value" => $value,
                        "name" => $name
                    ];
                }
                $returnData = [
                    "max" => $max,
                    "min" => 0,
                    "areaData" => $areaDataFormat,
                ];
                
                return result("", 1, $returnData);
            } else {
                return result("", -1);
            }
        } catch (\Exception $exception) {
            return result("", -1);
        }
    }
    
    //退货数据 统计列表
    public function cancelsList(Request $request)
    {
        $list = $this->stock->getLists($request, 2);
        $data = $list['list'];
        $count = $list['count'];
        
        return result("", 0, $data, $count);
    }
    
    
    //回收数据 柱状图 ->where('status', 1)
    public function retrieveData(Request $request)
    {
        try {
            
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $agentsMap[0] = '总数';
            
            //判断时间 getProvinceId
            $renewal_date = $request->datetime;
            $type = !empty($request->type) ? $request->type : 1;
            $time_type = !empty($request->time_type) ? $request->time_type : 1;
            if ($time_type) {
                $time = selectTimeRange($time_type);
                if ($time) {
                    $where[] = ['created_at', '>=', $time['start_time']];
                    $where[] = ['created_at', '<', $time['end_time']];
                } else {
                    $time_s_d = explode(' - ', $renewal_date);
                    $where[] = ['created_at', '>=', $time_s_d[0]];
                    $where[] = ['created_at', '<', $time_s_d[1]];    //考虑加一天，正好是第二天的零点
                }
            }
    
            if($type == 1) {
                $areaData = BlServiceRetrieve::selectRaw("agent_id, count(id) as nums ");
            }else{
                $areaData = BlServiceRetrieve::selectRaw("agent_id, SUM(num) as nums ");
            }
         
            $areaData->where($where)->where('status', 1)->groupBy("agent_id");
            $areaData = $areaData->pluck("nums", "agent_id")->toArray();
            
            $replaceArea = [];
            if (!empty($areaData)) {
                foreach ($areaData as $id => $num) {
                    $getProvinceIdOne = getProvinceId($id);
                    $replaceArea[][$getProvinceIdOne['name']] = $num;
                }
                $getProvinceData = comm_sumarrs($replaceArea);
                
                ksort($agentsMap);
                $total = 0;
                foreach ($agentsMap as $key => $name) {
                    $value = isset($getProvinceData[$name]) ? $getProvinceData[$name] : 0;
                    $per = $value;
                    $arrarea[] = $name;
                    $arrareadata[] = $value;
                    $total += $per;
                }
                
                $arrareadata[0] = $total;
                ksort($arrareadata);
                $supply = [$arrareadata[0]];
                $total = $arrareadata[0];
                foreach ($arrareadata as $k => $value) {
                    if ($k > 0) {
                        $supply[$k] = $total - $arrareadata[$k];
                        $total -= $arrareadata[$k];
                    }
                }
                $supply[0] = 0;
                //返回值是一个二维数组 分别放区域和数量
                $returnarea = [
                    'areaname' => $arrarea,
                    'areadata' => $arrareadata,
                    'areadatacum' => $supply,
                ];
                
                return result("", 1, $returnarea);
            } else {
                return result("", -1);
            }
        } catch (\Exception $exception) {
            return result("", -1);
        }
    }
    
    //回收数据 地图
    public function retrieveArea(Request $request)
    {
        try {
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $provincesArr = getAllProvincesName();
            //判断时间
            $renewal_date = $request->datetime;
            $areaData = BlServiceRetrieve::selectRaw("agent_id, count(id) as num ");
            $time_type = !empty($request->time_type) ? $request->time_type : 1;
            if ($time_type) {
                $time = selectTimeRange($time_type);
                if ($time) {
                    $where[] = ['created_at', '>=', $time['start_time']];
                    $where[] = ['created_at', '<', $time['end_time']];
                } else {
                    $time_s_d = explode(' - ', $renewal_date);
                    $where[] = ['created_at', '>=', $time_s_d[0]];
                    $where[] = ['created_at', '<', $time_s_d[1]];    //考虑加一天，正好是第二天的零点
                }
            }
            $areaData->where($where)->groupBy("agent_id");
            $areaData = $areaData->pluck("num", "agent_id")->toArray();
            $max = max($areaData);
            $min = min($areaData);
            if (!empty($areaData)) {
                foreach ($areaData as $id => $num) {
                    $getProvinceIdOne = getProvinceId($id);
                    $getProvinceData[][$getProvinceIdOne['name']] = $num;
                    
                }
                $replaceArea = comm_sumarrs($getProvinceData);
                
                ksort($agentsMap);
                $areaDataFormat = [];
                foreach ($provincesArr as $name) {
                    $value = isset($replaceArea[$name]) ? $replaceArea[$name] : 0;
                    $areaDataFormat[] = [
                        "value" => $value,
                        "name" => $name
                    ];
                }
                $returnData = [
                    "max" => $max,
                    "min" => 0,
                    "areaData" => $areaDataFormat,
                ];
                
                return result("", 1, $returnData);
            } else {
                return result("", -1);
            }
        } catch (\Exception $exception) {
            return result("", -1);
        }
    }

//回收数据 统计列表
    public function retrieveList(Request $request)
    {
        $list = $this->stock->getLists($request, 3);
        $data = $list['list'];
        $count = $list['count'];
        
        return result("", 0, $data, $count);
    }


//库存统计 柱状图
    public function statisticsData(Request $request)
    {
        try {
            
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $agentsMap[0] = '总数';
            
            //判断时间 getProvinceId
            $renewal_date = $request->datetime;
            $time_type = !empty($request->time_type) ? $request->time_type : 1;
            if ($time_type) {
                $time = selectTimeRange($time_type);
                if ($time) {
                    $where[] = ['created_at', '>=', $time['start_time']];
                    $where[] = ['created_at', '<', $time['end_time']];
                } else {
                    $time_s_d = explode(' - ', $renewal_date);
                    $where[] = ['created_at', '>=', $time_s_d[0]];
                    $where[] = ['created_at', '<', $time_s_d[1]];    //考虑加一天，正好是第二天的零点
                }
            }
            $areaData = BlServiceStockLease::selectRaw("agent_id, count(id) as num ");
            $areaData->where($where)->where('status', 1)->groupBy("agent_id");
            $areaData = $areaData->pluck("num", "agent_id")->toArray();
            
            $replaceArea = [];
            
            foreach ($areaData as $id => $num) {
                $getProvinceIdOne = getProvinceId($id);
                $replaceArea[][$getProvinceIdOne['name']] = $num;
            }
            $getProvinceData = comm_sumarrs($replaceArea);
            
            ksort($agentsMap);
            $total = 0;
            foreach ($agentsMap as $key => $name) {
                $value = isset($getProvinceData[$name]) ? $getProvinceData[$name] : 0;
                $per = $value;
                $arrarea[] = $name;
                $arrareadata[] = $value;
                $total += $per;
            }
            $arrareadata[0] = $total;
            ksort($arrareadata);
            $supply = [$arrareadata[0]];
            $total = $arrareadata[0];
            foreach ($arrareadata as $k => $value) {
                if ($k > 0) {
                    $supply[$k] = $total - $arrareadata[$k];
                    $total -= $arrareadata[$k];
                }
            }
            $supply[0] = 0;
            //返回值是一个二维数组 分别放区域和数量
            $returnarea = [
                'areaname' => $arrarea,
                'areadata' => $arrareadata,
                'areadatacum' => $supply,
            ];
            
            return result("", 1, $returnarea);
        } catch (\Exception $exception) {
            return result("", -1);
        }
    }
    
    //区域网点库存平均数 地图
    public function statisticsArea(Request $request)
    {
        try {
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $provincesArr = getAllProvincesName();
          
            //判断时间
            $renewal_date = $request->datetime;
            $areaData = BlServiceStockLog::selectRaw("agent_id, count(id) as num ");
            $time_type = !empty($request->time_type) ? $request->time_type : 1;
            if ($time_type) {
                $time = selectTimeRange($time_type);
                if ($time) {
                    $where[] = ['created_at', '>=', $time['start_time']];
                    $where[] = ['created_at', '<', $time['end_time']];
                } else {
                    $time_s_d = explode(' - ', $renewal_date);
                    $where[] = ['created_at', '>=', $time_s_d[0]];
                    $where[] = ['created_at', '<', $time_s_d[1]];    //考虑加一天，正好是第二天的零点
                }
            }
            $areaData->where($where)->groupBy("agent_id");
            $areaData = $areaData->pluck("num", "agent_id")->toArray();

            $getProvinceData = [];
            $nameNum = [];
            foreach ($areaData as $id => $num) {
                $getProvinceIdOne = getProvinceId($id);
                $getProvinceData[][$getProvinceIdOne['name']] = $num;
            }
            
            foreach ($agentsMap as $key=>$value){
                $total = 0;
                foreach ($getProvinceData as $k=>$v){
                        if(isset($v[$value])){
                            $total+=1;
                            $nameNum[$value] = $total;
                        }
                        
                        
                    }
            }
            
            $replaceArea = comm_sumarrs($getProvinceData);
          
            ksort($agentsMap);

            $areaDataFormat = [];
            $maxTotal = [];
            foreach ($provincesArr as $name) {
                $value = isset($replaceArea[$name]) ? $replaceArea[$name] : 0;
                //查询该省份所有网点个数，被除以数量，求平均值 round( $value/$nameNum[$name]),
                if($value != 0){
                    $areaDataFormat[] = [
                        "value" => round( $value/$nameNum[$name]),
                        "name" => $name
                    ];
                    $maxTotal[]=round( $value/$nameNum[$name]);
                }else{
                    $areaDataFormat[] = [
                        "value" => $value,
                        "name" => $name
                    ];
                }
            }
    
            $max = max($maxTotal);
            $returnData = [
                "max" => $max,
                "min" => 0,
                "areaData" => $areaDataFormat,
            ];
            
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result("", -1);
        }
    }
    
    //库存统计 统计列表
    public function statisticsList(Request $request)
    {
        $list = $this->stock->getStatisticsLists($request);
        $data = $list['list'];
        $count = $list['count'];
        
        return result("", 0, $data, $count);
    }
}



