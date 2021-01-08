<?php

namespace Modules\Manage\Http\Controllers\Lease;

use Illuminate\Support\Facades\DB;
use Modules\Manage\Models\Leaseprocess;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Manage\Models\LeaseRenewal;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseProcessChannel;

class LeaseproController extends Controller {
    protected $leaseUserModel;
    protected $effectContractStatus = '3,4,5,6,7,8';
    protected $contractModel;
    protected $hourArr = [
        "0:00",
        "1:00",
        "2:00",
        "3:00",
        "4:00",
        "5:00",
        "6:00",
        "7:00",
        "8:00",
        "9:00",
        "10:00",
        "11:00",
        "12:00",
        "13:00",
        "14:00",
        "15:00",
        "16:00",
        "17:00",
        "18:00",
        "19:00",
        "20:00",
        "21:00",
        "22:00",
        "23:00"
    ];
    protected $leaseprocess;

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    public function __construct(Leaseprocess $leaseprocess) {
        $this->leaseprocess = $leaseprocess;
        $this->leaserenewal = new LeaseRenewal();
        $this->leaseContractModel = new LeaseContract();
        
    }

    public function show() {
        $provinces = allUserProvinces();
        $timehourarr = timehourarr();
        $timehoursarr = timehoursarr();
        return view("manage::lease.report.leaseprocess")
            ->with("provinces", $provinces)
            ->with("timehourarr", $timehourarr)
            ->with("timehoursarr", $timehoursarr);
    }

    public function area() {
        $timeType = timeType();
        return view("manage::lease.report.leasearea")
            ->with("timeType", $timeType);
    }

    public function channel() {
        $timeType = timeType();
        $provinces = allUserProvinces();
        return view("manage::lease.report.leasechannel")
            ->with("provinces", $provinces)
            ->with("timeType", $timeType);
    }

    public function maturity() {
        $timeType = timeType();
        $provinces = allUserProvinces();
        return view("manage::lease.report.leasematurity")
            ->with("provinces", $provinces)
            ->with("timeType", $timeType);
    }

    public function register() {
        $timeType = timeType();
        $provinces = allUserProvinces();
        $timehoursarr = timehoursarr();
        return view("manage::lease.report.leaseregister")
            ->with("provinces", $provinces)
            ->with("timehoursarr", $timehoursarr)
            ->with("timeType", $timeType);
    }

    public function leasenewold() {
        $timeType = timeType();
        $provinces = allUserProvinces();
        $timehoursarr = timehoursarr();
        return view("manage::lease.report.leasenewold")
            ->with("provinces", $provinces)
            ->with("timehoursarr", $timehoursarr)
            ->with("timeType", $timeType);
    }

    public function Leaseregisterperiod() {
        $timeType = timeType();
        $provinces = allUserProvinces();
        $timehoursarr = timehoursarr();
        return view("manage::lease.report.leaseregisterperiod")
            ->with("provinces", $provinces)
            ->with("timehoursarr", $timehoursarr)
            ->with("timeType", $timeType);
    }


    //获取漏斗表的数据 1
    public function funnellists(Request $request) {
        $result = $this->leaseprocess->getfunnelList($request);
        if (!empty($result)) {
            return result('返回成功', 0, $result);
        }
        return result('暂无数据', 1, []);
    }

    //获取漏斗表的数据 2
    public function cumfunnellists(Request $request) {
        $result = $this->leaseprocess->getcumfunnelList($request);
        if (!empty($result)) {
            return result('返回成功', 0, $result);
        }
        return result('暂无数据', 1, []);
    }

    //获取漏斗表的数据 3
    public function totalfunnellists(Request $request) {
        $result = $this->leaseprocess->gettotalfunnelList($request);
        if (!empty($result)) {
            return result('返回成功', 0, $result);
        }
        return result('暂无数据', 1, []);
    }


    //获取列表数据 1
    public function lists(Request $request) {
        $result = $this->leaseprocess->getList($request);
        if (!empty($result)) {
            $data = $result['list'];
            $count = $result['count'];
            return result("", 0, $data, $count);
        }
        return result('暂无数据', 1, $result);

    }

    //获取列表数据 2 --累计每小时流失统计表
    public function cumlists(Request $request) {
        $result = $this->leaseprocess->getcumList($request);
        if (!empty($result)) {
            $data = $result['list'];
            $count = $result['count'];
            return result("", 0, $data, $count);
        }
        return result('暂无数据', 1, $result);
    }

    //获取列表数据 3 --累计每小时流失统计表
    public function totallists(Request $request) {
        $result = $this->leaseprocess->gettotalList($request);
        if (!empty($result)) {
            $data = $result['list'];
            $count = $result['count'];
            return result("", 0, $data, $count);
        }
        return result('暂无数据', 1, $result);

    }

    //租赁区域分布  地图
    public function areadata(Request $request) {
        try {
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $provincesArr = getAllProvincesName();
            //判断时间
            $renewal_date = $request->datetime;
            $areaData = LeaseContract::selectRaw("province_id, count(id) as num ");
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
            $areaData->where($where)->groupBy("province_id");
            $areaData = $areaData->pluck("num", "province_id")->toArray();
            $max = max($areaData);
            $min = min($areaData);
            $replaceArea = [];
            foreach ($areaData as $id => $num) {
                $replaceArea[$agentsMap[$id]] = $num;
            }
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
        } catch (\Exception $exception) {
            return result("", -1);
        }
    }

    //区域租赁数  统计表
    public function areadatalist(Request $request) {
        try {
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $provincesArr = getAllProvincesName();
            //判断时间
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
            $areaData = LeaseContract::selectRaw("province_id, count(id) as num ");
            $areaData->where($where)->groupBy("province_id");
            $areaData = $areaData->pluck("num", "province_id")->toArray();
            $replaceArea = [];
            foreach ($areaData as $id => $num) {
                $replaceArea[$agentsMap[$id]] = $num;
            }
            $areaDataFormat = [];
            foreach ($agentsMap as $name) {
                $value = isset($replaceArea[$name]) ? $replaceArea[$name] : 0;
                $arrarea[] = $name;
                $arrareadata[] = $value;
            }
            //返回值是一个二维数组 分别放区域和数量
            $returnarea = [
                'areaname' => $arrarea,
                'areadata' => $arrareadata,
            ];
            return result("", 1, $returnarea);
        } catch (\Exception $exception) {
            return result("", -1);
        }

    }


    //各渠道转化  柱状图
    public function channeldata(Request $request) {
        try {
            //判断时间
            $renewal_date = $request->datetime;
            $time_type = !empty($request->time_type) ? $request->time_type : 1;
            if ($time_type) {
                $time = selectTimeRange($time_type);
                if ($time) {
                    $where[] = ['process_date', '>=', $time['start_time']];
                    $where[] = ['process_date', '<', $time['end_time']];
                } else {
                    $time_s_d = explode(' - ', $renewal_date);
                    $where[] = ['process_date', '>=', $time_s_d[0]];
                    $where[] = ['process_date', '<', $time_s_d[1]];
                }
            }
            //按区域进行查询
            if ($request->area != "" ) {
                $where[] = ['province_id', $request->area];
            }
            //查询
            $data = LeaseProcessChannel::select(
                    array(
                        \DB::raw('systemtype'),
                        \DB::raw('SUM(pay_num) as user_idnum'),
                    )
                )
                ->where($where)
                ->groupBy('systemtype')
                ->get()
                ->toarray();
            
            $namedata = [];
            $numdata = [];
            if (!empty($data)) {
                foreach ($data as $key=>$value) {
                        $namedata[] =  $value['systemtype'];
                        $numdata[] = $value['user_idnum'];
                }
                
                //返回一个二维数组 分别放区域和数量
                $returnarea = [
                    'areaname' => $namedata,
                    'areadata' => $numdata,
                ];
 
                return result("", 1, $returnarea);
            } else {
                return result("", -1);
            }
        } catch (\Exception $exception) {
            return result("", -1);
        }

    }


    //各渠道转化统计表  列表
    public function channeldatalist(Request $request) {
        $list = $this->leaseprocess->getChannelLists($request, "lists");
        $data = $list['list'];
        $count = $list['count'];
        return result("", 0, $data, $count);
    }


    //到期转化图  饼状图
    public function maturitydata(Request $request) {
        $data = $this->leaseprocess->getHistogram($request, "lists");
        return result("", 0, $data);

    }

    //到期转化统计表  列表
    public function maturitydatalist(Request $request) {
        $list = $this->leaseprocess->getHistogramLists($request, "lists");
        $data = $list['list'];
        $count = $list['count'];
        return result("", 0, $data, $count);
    }

    //注册租赁  折线图
    public function RegisterTimeHour(Request $request) {
        try {
            $req = $request->all();
            $defaultDay = [
                date("Y-m-d", strtotime("-1 day")),
                date("Y-m-d")
            ];
            if (isset($req["dayArr"]) && $req["dayArr"]) {
                $defaultDay = array_merge($req["dayArr"], $defaultDay);
            }
            $defaultDayStr = "'" . implode("','", $defaultDay) . "'";
            $whereSql = "";
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $whereSql = " province_id = " . $req["agentId"] . ' and ';
            }
            $sql = " SELECT SUM(pay_num) as num,process_date,insert_hour as hour,"
                . " DATE_FORMAT(from_unixtime(created_at), '%Y-%m-%d %H:00:00') as time FROM lease_process_hour_registers "
                . " where  $whereSql  "
                . " process_date in($defaultDayStr) GROUP BY time";
            $data = DB::select($sql);
            $series = [];
            $defaultNumArr = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($defaultDay as $k => $day) {
                $series[$k] = [
                    "name" => $day,
                    "type" => 'line',
                    "stack" => $day,
                    "symbolSize" => 8,
                    "symbol" => 'circle',
                    "data" => $defaultNumArr
                ];
                foreach ($data as $item) {
                    if ($item->process_date == $day) {
                        $series[$k]["data"][$item->hour * 1] = $item->num;
                    }
                }
            }
            $returnData = [
                "days" => $defaultDay,
                "hourArr" => $this->hourArr,
                "series" => $series,
            ];

            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }


    //注册租赁  累计每小时转化 折线图
    public function RegisterTimeHours(Request $request) {
        try {
            $req = $request->all();
            $defaultDay = [
                date("Y-m-d", strtotime("-1 day")),
                date("Y-m-d")
            ];
            if (isset($req["dayArr"]) && $req["dayArr"]) {
                $defaultDay = array_merge($req["dayArr"], $defaultDay);
            }
            $defaultDayStr = "'" . implode("','", $defaultDay) . "'";
            $whereSql = "";
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all" ) {
                $whereSql = " province_id = " . $req["agentId"] . ' and ';
            }
            $sql = " SELECT SUM(pay_num) as num,process_date,insert_hour as hour,"
                . " DATE_FORMAT(from_unixtime(created_at), '%Y-%m-%d %H:00:00') as time FROM lease_process_hour_registers "
                . " where  $whereSql  " . " process_date in($defaultDayStr) GROUP BY time";
            $data = DB::select($sql);

            $series = [];
            $defaultNumArr = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            
            foreach ($defaultDay as $k => $day) {
                $series[$k] = [
                    "name" => $day,
                    "type" => 'line',
                    "stack" => $day,
                    "symbolSize" => 8,
                    "symbol" => 'circle',
                    "data" => $defaultNumArr
                ];
                $totalNum = 0;
                foreach ($data as $item) {
                    if ($item->process_date == $day) {
                        $totalNum =$totalNum + $item->num;
                        $series[$k]["data"][$item->hour * 1] = $totalNum;
                    }
                }
            }
            $returnData = [
                "days" => $defaultDay,
                "hourArr" => $this->hourArr,
                "series" => $series,
            ];

            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }
    
    //注册租赁 //转化统计图 柱状图
    public function RegisterTimeCum(Request $request) {
        try {
            $req = $request->all();
            $defaultDay = date("Y-m-d");
            if (isset($req["dayArr"]) && $req["dayArr"]) {
                $defaultDay = $req["dayArr"];
            }
            $defaultDayStr = $defaultDay;
            $whereSql = "";
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $whereSql =  ' and '." province_id = " . $req["agentId"] ;
            }
            $sql = " SELECT SUM(register_num) as register_num,SUM(login_num) as login_num,SUM(index_num) as index_num,SUM(period_num) as period_num,SUM(deduction_num) as deduction_num,SUM(submit_lease_num) as submit_lease_num,SUM(mylease_num) as mylease_num,SUM(business_num) as business_num,SUM(topay_num) as topay_num,SUM(dopay_num) as dopay_num,SUM(pay_num) as pay_num"
                . "  FROM lease_process_hour_registers "  . " where process_date = '$defaultDayStr'" . "   $whereSql  ";
            $data = DB::select($sql);
          
            if (!empty($data)) {
                $data = objectToArray($data);
                $numarr = [];
                foreach ($data[0] as $key) {
                    $numarr[] = $key;
                }
            }

            $defaultNumArr = [
                '注册成功',
                '登录成功',
                '到达首页',
                '选择租赁周期',
                '旧电池抵扣',
                '提交租赁单',
                '我的租赁页面',
                '商家扫码确认',
                '租赁待支付',
                '发起支付',
                '支付成功'
            ];
            $returnData = [
                "headname" => $defaultNumArr,
                "datanum" => $numarr,
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //注册租赁 每小时转化统计表 列表
    public function RegisterTimeHourlist(Request $request) {
        $result = $this->leaseprocess->TimeHourlist($request);
        if (!empty($result)) {
            $data = $result['list'];
            $count = $result['count'];
            return result("", 0, $data, $count);
        }
        return result('暂无数据', 1, $result);
    }

    //注册租赁 每小时转化统计表 列表
    public function RegisterTimeHourslist(Request $request) {
        $result = $this->leaseprocess->TimeHourslist($request);
        if (!empty($result)) {
            $data = $result['list'];
            $count = $result['count'];
            return result("", 0, $data, $count);
        }
        return result('暂无数据', 1, $result);
    }

    //注册租赁 每日转化统计表 列表
    public function RegisterTimeDayslist(Request $request) {
        $result = $this->leaseprocess->TimeDayslist($request);
        if (!empty($result)) {
            $data = $result['list'];
            $count = $result['count'];
            return result("", 0, $data, $count);
        }
        return result('暂无数据', 1, $result);
    }

    //租赁业务  新老用户租赁对比
    public function LeaseNewOldlist(Request $request) {
        $data = $this->leaseprocess->getnewoldlists($request);
        if (!empty($data)) {
            return result("", 1, $data);
        }
        return result('', 0, []);
    }
    
    //租赁业务  新老用户租赁对比 新老合并成为新趋势
    public function getNewOldTotalLists(Request $request) {
        $data = $this->leaseprocess->getNewOldTotalLists($request);
        if (!empty($data)) {
            return result("", 1, $data);
        }
        return result('', 0, []);
    }
    
    

    //租赁业务  新老用户租赁对比
    public function LeaseNewOldMoneylist(Request $request) {
        $data = $this->leaseprocess->getnewoldmoneylists($request);
        if (!empty($data)) {
            return result("", 1, $data);
        }
        return result('', 0, []);
    }

    //租赁业务  注册-租赁发起周期的数据
    public function RegisterPeriodlist(Request $request) {
        $result = $this->leaseprocess->getPeriodlist($request);
        if (!empty($result)) {
            $data = $result['list'];
            $count = $result['count'];
            return result("", 0, $data, $count);
        }
        return result('暂无数据', 1, $result);
    }

}
