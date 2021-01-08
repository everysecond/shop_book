<?php

namespace Modules\Manage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseContractDateInfo;

class Leaseprocess extends Model
{
    protected $table = "lease_process_hours";
    protected $table_register = "lease_process_hour_registers";
    protected $table_register_period = "lease_process_register_periods";
    protected $table_renewal = "lease_renewal";
    protected $table_lease_contracts = "lease_contracts";
    protected $table_lease_channels = "lease_process_channels";
    protected $table_lease_contract_date_infos = "lease_contract_date_infos";
    
    public $timestamps = false;
    //有效合约状态
    protected $effectContractStatus = '3,4,5,7,8';
    
    //获取漏斗数据 1
    public function getfunnelList($request)
    {
        //默认查询是 当前所在小时区间() 地址选择为全部区域
        $query = DB::table($this->table);
        //区分全部区域还有各个省的
        //按指定时间进行查询
        if ($request->datetime != "") {
            $query->where('insert_hour', $request->datetime);
        } else {
            $query->where('insert_hour', date('H',strtotime("-1 hour")));
        }
        //按区域进行查询
        if ($request->area != "") {
            $area = $request->area;
            $query->where('province_id', $area);
        }
        $data = objectToArray($query->select('login_num', 'index_num', 'scan_num', 'detail_num',
            'period_num', 'deduction_num', 'submit_lease_num', 'business_num', 'topay_num',
            'pay_num')->where('process_date', date('Y-m-d'))->get()->toArray());
        if (!empty($data)) {
            $loginsum = 0;
            $indexsum = 0;
            $scansum = 0;
            $detailsum = 0;
            $periodsum = 0;
            $deductionsum = 0;
            $submit_leasesum = 0;
            $businesssum = 0;
            $topaysum = 0;
            $paysum = 0;
            foreach ($data as $val) {
                $loginsum += $val['login_num']; //求和 所有到达登录页
                $indexsum += $val['index_num']; //求和
                $scansum += $val['scan_num']; //求和
                $detailsum += $val['detail_num']; //求和
                $periodsum += $val['period_num']; //求和
                $deductionsum += $val['deduction_num']; //求和
                $submit_leasesum += $val['submit_lease_num']; //求和
                $businesssum += $val['business_num']; //求和
                $topaysum += $val['topay_num']; //求和
                $paysum += $val['pay_num']; //求和
            }
            //先算value总和
            $DataSum = $loginsum + $indexsum + $scansum + $detailsum + $periodsum + $deductionsum + $submit_leasesum + $businesssum + $topaysum + $paysum;
            //求出最大值：
            $max = max($loginsum, $indexsum, $scansum, $detailsum, $periodsum, $deductionsum, $submit_leasesum,
                $businesssum, $topaysum, $paysum);
            $max100 = round(($max / $DataSum) * 100);
            //拼接数组
            $DataSumArr = array(
                [
                    'value' => round(($loginsum / $DataSum) * 100),
                    'name' => '到达登录页 ' . $loginsum . ' ,' . round(($loginsum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($indexsum / $DataSum) * 100),
                    'name' => '到达首页  ' . $indexsum . ' ,' . round(($indexsum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($scansum / $DataSum) * 100),
                    'name' => '到达扫码页  ' . $scansum . ' ,' . round(($scansum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($detailsum / $DataSum) * 100),
                    'name' => '到达租赁详情  ' . $detailsum . ' ,' . round(($detailsum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($periodsum / $DataSum) * 100),
                    'name' => '选择租赁周期  ' . $periodsum . ' ,' . round(($periodsum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($deductionsum / $DataSum) * 100),
                    'name' => '选择旧电池抵扣  ' . $deductionsum . ' ,' . round(($deductionsum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($submit_leasesum / $DataSum) * 100),
                    'name' => '提交租赁单  ' . $submit_leasesum . ' ,' . round(($submit_leasesum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($businesssum / $DataSum) * 100),
                    'name' => '商家确认  ' . $businesssum . ' ,' . round(($businesssum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($topaysum / $DataSum) * 100),
                    'name' => '租赁待支付  ' . $topaysum . ' ,' . round(($topaysum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($paysum / $DataSum) * 100),
                    'name' => '租赁支付  ' . $paysum . ' ,' . round(($paysum / $DataSum) * 100) . '%'
                ]
            );
            
            $dataend = [$DataSumArr, $max100];
            return $dataend;
        } else {
            return array();
        }
    }
    
    //获取累计漏斗数据 2
    public function getcumfunnelList($request)
    {
        //默认查询是 当前所在累计小时区间(0-5)  地址选择为全部区域
        $query = DB::table($this->table);
        //按指定所在累计小时区间进行查询
        if ($request->datetime != "") {
            $query->where('insert_hour', '<=', $request->datetime);
        } else {
            $query->where('insert_hour', '<=', date('H'));
        }
        //按区域进行查询
        if ($request->area != "") {
            $area = $request->area;
            $query->where('province_id', $area);
        }
        $data = objectToArray($query->select('login_num', 'index_num', 'scan_num', 'detail_num',
            'period_num', 'deduction_num', 'submit_lease_num', 'business_num', 'topay_num',
            'pay_num')->where('process_date', date('Y-m-d'))->get()->toArray());
        if (!empty($data)) {
            $loginsum = 0;
            $indexsum = 0;
            $scansum = 0;
            $detailsum = 0;
            $periodsum = 0;
            $deductionsum = 0;
            $submit_leasesum = 0;
            $businesssum = 0;
            $topaysum = 0;
            $paysum = 0;
            foreach ($data as $val) {
                $loginsum += $val['login_num']; //求和 所有到达登录页
                $indexsum += $val['index_num']; //求和
                $scansum += $val['scan_num']; //求和
                $detailsum += $val['detail_num']; //求和
                $periodsum += $val['period_num']; //求和
                $deductionsum += $val['deduction_num']; //求和
                $submit_leasesum += $val['submit_lease_num']; //求和
                $businesssum += $val['business_num']; //求和
                $topaysum += $val['topay_num']; //求和
                $paysum += $val['pay_num']; //求和
            }
            //先算value总和
            $DataSum = $loginsum + $indexsum + $scansum + $detailsum + $periodsum + $deductionsum + $submit_leasesum + $businesssum + $topaysum + $paysum;
            //求出最大值：
            $max = max($loginsum, $indexsum, $scansum, $detailsum, $periodsum, $deductionsum, $submit_leasesum,
                $businesssum, $topaysum, $paysum);
            $max100 = round(($max / $DataSum) * 100);
            //拼接数组
            $DataSumArr = array(
                [
                    'value' => round(($loginsum / $DataSum) * 100),
                    'name' => '到达登录页 ' . $loginsum . ' ,' . round(($loginsum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($indexsum / $DataSum) * 100),
                    'name' => '到达首页  ' . $indexsum . ' ,' . round(($indexsum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($scansum / $DataSum) * 100),
                    'name' => '到达扫码页  ' . $scansum . ' ,' . round(($scansum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($detailsum / $DataSum) * 100),
                    'name' => '到达租赁详情  ' . $detailsum . ' ,' . round(($detailsum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($periodsum / $DataSum) * 100),
                    'name' => '选择租赁周期  ' . $periodsum . ' ,' . round(($periodsum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($deductionsum / $DataSum) * 100),
                    'name' => '选择旧电池抵扣  ' . $deductionsum . ' ,' . round(($deductionsum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($submit_leasesum / $DataSum) * 100),
                    'name' => '提交租赁单  ' . $submit_leasesum . ' ,' . round(($submit_leasesum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($businesssum / $DataSum) * 100),
                    'name' => '商家确认  ' . $businesssum . ' ,' . round(($businesssum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($topaysum / $DataSum) * 100),
                    'name' => '租赁待支付  ' . $topaysum . ' ,' . round(($topaysum / $DataSum) * 100) . '%'
                ],
                [
                    'value' => round(($paysum / $DataSum) * 100),
                    'name' => '租赁支付  ' . $paysum . ' ,' . round(($paysum / $DataSum) * 100) . '%'
                ]
            );
            
            $dataend = [$DataSumArr, $max100];
            return $dataend;
        } else {
            return array();
        }
    }
    
    //获取累计漏斗数据 3
    public function gettotalfunnelList($request)
    {
        //默认查询是 当天() 地址选择为全部区域
        $query = DB::table($this->table);
        //按指定时间进行查询
        if ($request->datetime != "") {
            $query->where('process_date', $request->datetime);
        } else {
            $query->where('process_date', date('Y-m-d'));
        }
        //按区域进行查询
        if ($request->area != "") {
            $area = $request->area;
            $query->where('province_id', $area);
        }
        $data = objectToArray($query->select('login_num', 'index_num', 'scan_num', 'detail_num',
            'period_num', 'deduction_num', 'submit_lease_num', 'business_num', 'topay_num',
            'pay_num')->get()->toArray());
        if (!empty($data)) {
            $loginsum = 0;
            $indexsum = 0;
            $scansum = 0;
            $detailsum = 0;
            $periodsum = 0;
            $deductionsum = 0;
            $submit_leasesum = 0;
            $businesssum = 0;
            $topaysum = 0;
            $paysum = 0;
            foreach ($data as $val) {
                $loginsum += $val['login_num']; //求和 所有到达登录页
                $indexsum += $val['index_num']; //求和
                $scansum += $val['scan_num']; //求和
                $detailsum += $val['detail_num']; //求和
                $periodsum += $val['period_num']; //求和
                $deductionsum += $val['deduction_num']; //求和
                $submit_leasesum += $val['submit_lease_num']; //求和
                $businesssum += $val['business_num']; //求和
                $topaysum += $val['topay_num']; //求和
                $paysum += $val['pay_num']; //求和
            }
            //先算value总和
            $DataSum = $loginsum + $indexsum + $scansum + $detailsum + $periodsum + $deductionsum + $submit_leasesum + $businesssum + $topaysum + $paysum;
            //求出最大值：
            $max = max($loginsum, $indexsum, $scansum, $detailsum, $periodsum, $deductionsum, $submit_leasesum,
                $businesssum, $topaysum, $paysum);
            $max100 = round(($max / $DataSum) * 100);
            //拼接数组
            $DataSumArr = array(
                [
                    'value' => round(($loginsum / $DataSum) * 100),
                    'name' => '到达登录页 ' . $loginsum . ' 人，占比' . round(($loginsum / $loginsum) * 100) . '%'
                ],
                [
                    'value' => round(($indexsum / $DataSum) * 100),
                    'name' => '到达首页  ' . $indexsum . ' 人，占比' . round(($indexsum / $loginsum) * 100) . '%'
                ],
                [
                    'value' => round(($scansum / $DataSum) * 100),
                    'name' => '到达扫码页  ' . $scansum . '人，占比' . round(($scansum / $loginsum) * 100) . '%'
                ],
                [
                    'value' => round(($detailsum / $DataSum) * 100),
                    'name' => '到达租赁详情  ' . $detailsum . '人，占比' . round(($detailsum / $loginsum) * 100) . '%'
                ],
                [
                    'value' => round(($periodsum / $DataSum) * 100),
                    'name' => '选择租赁周期  ' . $periodsum . '人，占比' . round(($periodsum / $loginsum) * 100) . '%'
                ],
                [
                    'value' => round(($deductionsum / $DataSum) * 100),
                    'name' => '选择旧电池抵扣  ' . $deductionsum . '人，占比' . round(($deductionsum / $loginsum) * 100) . '%'
                ],
                [
                    'value' => round(($submit_leasesum / $DataSum) * 100),
                    'name' => '提交租赁单  ' . $submit_leasesum . '人，占比' . round(($submit_leasesum / $loginsum) * 100) . '%'
                ],
                [
                    'value' => round(($businesssum / $DataSum) * 100),
                    'name' => '商家确认  ' . $businesssum . '人，占比' . round(($businesssum / $loginsum) * 100) . '%'
                ],
                [
                    'value' => round(($topaysum / $DataSum) * 100),
                    'name' => '租赁待支付  ' . $topaysum . '人，占比' . round(($topaysum / $loginsum) * 100) . '%'
                ],
                [
                    'value' => round(($paysum / $DataSum) * 100),
                    'name' => '租赁支付  ' . $paysum . '人，占比' . round(($paysum / $loginsum) * 100) . '%'
                ]
            );
            $dataend = [$DataSumArr, $max100];
            return $dataend;
        } else {
            return array();
        }
    }
    
    
    //获取列表数据
    public function getList($request)
    {
        //默认查询当天每小时流失统计表
        $query = DB::table($this->table);
        //按指定时间进行查询
        if ($request->todatetime != "") {
            $query->where('process_date', $request->todatetime);
        } else {
            $query->where('process_date', date('Y-m-d'));
        }
        
        //按区域进行查询
        if ($request->toarea != "") {
            $area = $request->toarea;
            $query->where('province_id', $area);
        }
        
        $pageNum = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 10;
        $page = $pageNum - 1;
        if ($page != 0) {
            $page = $limit * $page;
            
        }
        $query1 = clone $query;
        $list = $query
            ->select(
                array(
                    \DB::raw('insert_hour as insert_hour'),
                    \DB::raw('SUM(login_num) as login_nums'),
                    \DB::raw('SUM(index_num) as index_num'),
                    \DB::raw('SUM(scan_num) as scan_num'),
                    \DB::raw('SUM(detail_num) as detail_num'),
                    \DB::raw('SUM(period_num) as period_num'),
                    \DB::raw('SUM(deduction_num) as deduction_num'),
                    \DB::raw('SUM(submit_lease_num) as submit_lease_num'),
                    \DB::raw('SUM(business_num) as business_num'),
                    \DB::raw('SUM(topay_num) as topay_num'),
                    \DB::raw('SUM(dopay_num) as dopay_num'),
                    \DB::raw('SUM(pay_num) as pay_nums'),
                    \DB::raw('SUM(pay_num)/SUM(login_num) as divpay_num')
                )
            )
            ->groupBy('insert_hour')
            ->offset($page)->limit($limit)->get()->toArray();
        $count = $query1->select('id')->groupBy('insert_hour')->get()->count();
        $data['list'] = $list;
        $data['count'] = $count;
        return $data;
    }
    
    //获取累计列表数据  --累计每小时流失统计表
    public function getcumList($request)
    {
        //默认查询当天每小时流失统计表
        $query = DB::table($this->table)->where('process_date', date('Y-m-d'));
        //按指定时间进行查询
        if ($request->datetime != "") {
            $query->where('insert_hour', '<=', $request->datetime);
        } else {
            $query->where('insert_hour', '<=', date('H'));
        }
        //按区域进行查询
        if ($request->area != "") {
            $query->where('province_id', $request->area);
        }
        $pageNum = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 10;
        $page = $pageNum - 1;
        if ($page != 0) {
            $page = $limit * $page;
            
        }
        $query1 = clone $query;
        $list = objectToArray($query->select(
            array(
                \DB::raw('insert_hour as insert_hour'),
                \DB::raw('SUM(login_num) as login_num'),
                \DB::raw('SUM(index_num) as index_num'),
                \DB::raw('SUM(scan_num) as scan_num'),
                \DB::raw('SUM(detail_num) as detail_num'),
                \DB::raw('SUM(period_num) as period_num'),
                \DB::raw('SUM(deduction_num) as deduction_num'),
                \DB::raw('SUM(submit_lease_num) as submit_lease_num'),
                \DB::raw('SUM(business_num) as business_num'),
                \DB::raw('SUM(topay_num) as topay_num'),
                \DB::raw('SUM(pay_num) as pay_num'),
            )
        )
            ->groupBy('insert_hour')
            ->offset($page)->limit($limit)->get()->toArray());
        
        foreach ($list as $key => $value) {
            if ($key != 0) {
                $list[$key]['login_num'] += $list[$key - 1]['login_num'];
                $list[$key]['index_num'] += $list[$key - 1]['index_num'];
                $list[$key]['scan_num'] += $list[$key - 1]['scan_num'];
                $list[$key]['detail_num'] += $list[$key - 1]['detail_num'];
                $list[$key]['period_num'] += $list[$key - 1]['period_num'];
                $list[$key]['deduction_num'] += $list[$key - 1]['deduction_num'];
                $list[$key]['submit_lease_num'] += $list[$key - 1]['submit_lease_num'];
                $list[$key]['business_num'] += $list[$key - 1]['business_num'];
                $list[$key]['topay_num'] += $list[$key - 1]['topay_num'];
                $list[$key]['pay_num'] += $list[$key - 1]['pay_num'];
            }
        }
        
        $count = $query1->select('id')->groupBy('insert_hour')->get()->count();
        $data['list'] = $list;
        $data['count'] = $count;
        return $data;
    }
    
    //获取累计列表数据 3  ---    转化统计表
    public function gettotalList($request)
    {
        //默认查询当天转化统计表
        $query = DB::table($this->table);
        //按指定时间进行查询
        if ($request->datetime != "") {
            $datatime = $request->datetime;
            $floatStartDate = substr($datatime, 0, 10);
            $startDate = $floatStartDate . " 00:00:00";
            $floatEndDate = substr($datatime, -10);
            $endDate = $floatEndDate . " 00:00:00";
            if (strtotime($startDate) > time()) {
            
            } elseif (strtotime($startDate) > strtotime($endDate)) {
            
            } else {
                //处理时间包含开始时间和结束时间
                $startDateStr = strtotime($startDate) - 1;
                $endDateStr = strtotime($endDate) + (24 * 3600 - 1);
                $startDate = date("Y-m-d H:i:s", $startDateStr);
                $endDate = date("Y-m-d H:i:s", $endDateStr);
                $query->whereBetween('process_date', [$startDate, $endDate]);
            }
        } else {
            $query->where('process_date', date('Y-m-d'));
        }
        
        //按区域进行查询
        if ($request->area != "") {
            $query->where('province_id', $request->area);
        }
        $count = $query->count();
        $pageNum = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 10;
        $page = $pageNum - 1;
        if ($page != 0) {
            $page = $limit * $page;
        }
        $query1 = clone $query;
        $list = $query
            ->select(
                array(
                    \DB::raw('process_date as process_date'),
                    \DB::raw('SUM(login_num) as login_nums'),
                    \DB::raw('SUM(index_num) as index_num'),
                    \DB::raw('SUM(scan_num) as scan_num'),
                    \DB::raw('SUM(detail_num) as detail_num'),
                    \DB::raw('SUM(period_num) as period_num'),
                    \DB::raw('SUM(deduction_num) as deduction_num'),
                    \DB::raw('SUM(submit_lease_num) as submit_lease_num'),
                    \DB::raw('SUM(business_num) as business_num'),
                    \DB::raw('SUM(topay_num) as topay_num'),
                    \DB::raw('SUM(dopay_num) as dopay_num'),
                    \DB::raw('SUM(pay_num) as pay_nums'),
                    \DB::raw('SUM(pay_num)/SUM(login_num) as divpay_num'),
                )
            )
            ->groupBy('process_date')
            ->offset($page)
            ->limit($limit)
            ->get()
            ->toArray();
        
        
        $count = $query1->select('id')->groupBy('process_date')->get()->count();
        $data['list'] = $list;
        $data['count'] = $count;
        return $data;
    }
    
    //转化统计表
    public function TimeHourlist($request)
    {
        //默认查询当天转化统计表
        $query = DB::table($this->table_register);
        //按指定时间进行查询
        if ($request->datetime != "") {
            $datatime = $request->datetime;
            $query->where('process_date', $datatime);
        } else {
            $query->where('process_date', date('Y-m-d'));
        }
        //按区域进行查询
        if ($request->area != "" ) {
            $query->where('province_id', $request->area);
        }
        $pageNum = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 10;
        $page = $pageNum - 1;
        if ($page != 0) {
            $page = $limit * $page;
        }
        $query1 = clone $query;
        $list = $query
            ->select(
                array(
                    \DB::raw('insert_hour'),
                    \DB::raw('SUM(register_num) as register_nums'),
                    \DB::raw('SUM(login_num) as login_nums'),
                    \DB::raw('SUM(index_num) as index_num'),
                    \DB::raw('SUM(scan_num) as scan_num'),
                    \DB::raw('SUM(detail_num) as detail_num'),
                    \DB::raw('SUM(period_num) as period_num'),
                    \DB::raw('SUM(deduction_num) as deduction_num'),
                    \DB::raw('SUM(submit_lease_num) as submit_lease_num'),
                    \DB::raw('SUM(mylease_num) as mylease_num'),
                    \DB::raw('SUM(business_num) as business_num'),
                    \DB::raw('SUM(topay_num) as topay_num'),
                    \DB::raw('SUM(dopay_num) as dopay_num'),
                    \DB::raw('SUM(pay_num) as pay_nums'),
                    \DB::raw('SUM(pay_num)/SUM(register_num) as divpay_num'),
                )
            )
            ->groupBy('insert_hour')
            ->offset($page)
            ->limit($limit)
            ->get()
            ->toArray();
        $count = $query1->select('id')->groupBy('insert_hour')->get()->count();
        $data['list'] = $list;
        $data['count'] = $count;
        return $data;
    }
    
    //累计转化统计表
    public function TimeHourslist($request)
    {
        //默认查询当天转化统计表
        $query = DB::table($this->table_register);
        //按指定时间进行查询
        if ($request->datetime != "") {
            $datatime = $request->datetime;
            $query->where('process_date', $datatime);
        } else {
            $query->where('process_date', date('Y-m-d'));
        }
        
        //按区域进行查询
        if ($request->area != "" ) {
            $query->where('province_id', $request->area);
        }
        $pageNum = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 10;
        $page = $pageNum - 1;
        if ($page != 0) {
            $page = $limit * $page;
        }
        $query1 = clone $query;
        $list = $query
            ->select(
                array(
                    \DB::raw('insert_hour '),
                    \DB::raw('SUM(register_num) as register_nums'),
                    \DB::raw('SUM(login_num) as login_nums'),
                    \DB::raw('SUM(index_num) as index_num'),
                    \DB::raw('SUM(scan_num) as scan_num'),
                    \DB::raw('SUM(detail_num) as detail_num'),
                    \DB::raw('SUM(period_num) as period_num'),
                    \DB::raw('SUM(deduction_num) as deduction_num'),
                    \DB::raw('SUM(submit_lease_num) as submit_lease_num'),
                    \DB::raw('SUM(mylease_num) as mylease_num'),
                    \DB::raw('SUM(business_num) as business_num'),
                    \DB::raw('SUM(topay_num) as topay_num'),
                    \DB::raw('SUM(dopay_num) as dopay_num'),
                    \DB::raw('SUM(pay_num) as pay_nums'),
                    \DB::raw('SUM(pay_num)/SUM(register_num) as divpay_num'),
                )
            )
            ->groupBy('insert_hour')
            ->offset($page)
            ->limit($limit)
            ->get()
            ->toArray();
        $list = objectToArray($list);
        $totalregister_nums = 0;
        $totallogin_nums = 0;
        $totalindex_num = 0;
        $totalscan_num = 0;
        $totaldetail_num = 0;
        $totalperiod_num = 0;
        $totaldeduction_num = 0;
        $totalsubmit_lease_num = 0;
        $totalmylease_num = 0;
        $totalbusiness_num = 0;
        $totaltopay_num = 0;
        $totaldopay_num = 0;
        $totalpay_nums = 0;
        foreach ($list as $k => $item) {
            if($item != 0) {
                $totalregister_nums += $item['register_nums'];
                $totallogin_nums += $item['login_nums'];
                $totalindex_num += $item['index_num'];
                $totalscan_num += $item['scan_num'];
                $totaldetail_num += $item['detail_num'];
                $totalperiod_num += $item['period_num'];
                $totaldeduction_num += $item['deduction_num'];
                $totalsubmit_lease_num += $item['submit_lease_num'];
                $totalmylease_num += $item['mylease_num'];
                $totalbusiness_num += $item['business_num'];
                $totaltopay_num += $item['topay_num'];
                $totaldopay_num += $item['dopay_num'];
                $totalpay_nums += $item['pay_nums'];
                $list[$k]['register_nums'] = $totalregister_nums;
                $list[$k]['login_nums'] = $totallogin_nums;
                $list[$k]['index_num'] = $totalindex_num;
                $list[$k]['scan_num'] = $totalscan_num;
                $list[$k]['detail_num'] = $totaldetail_num;
                $list[$k]['period_num'] = $totalperiod_num;
                $list[$k]['deduction_num'] = $totaldeduction_num;
                $list[$k]['submit_lease_num'] = $totalsubmit_lease_num;
                $list[$k]['mylease_num'] = $totalmylease_num;
                $list[$k]['business_num'] = $totalbusiness_num;
                $list[$k]['topay_num'] = $totaltopay_num;
                $list[$k]['dopay_num'] = $totaldopay_num;
                $list[$k]['dopay_nums'] = $totalpay_nums;
            }
        }
        $count = $query1->select('id')->groupBy('insert_hour')->get()->count();
        $data['list'] = $list;
        $data['count'] = $count;
        return $data;
    }
    
    //每日转化统计表  ---    转化统计表
    public function TimeDayslist($request)
    {
        //默认查询当天转化统计表
        $query = DB::table($this->table_register);
        //按指定时间进行查询
        if ($request->datetime != "") {
            $datatime = $request->datetime;
            $floatStartDate = substr($datatime, 0, 10);
            $startDate = $floatStartDate . " 00:00:00";
            $floatEndDate = substr($datatime, -10);
            $endDate = $floatEndDate . " 00:00:00";
            if (strtotime($startDate) > time()) {
            
            } elseif (strtotime($startDate) > strtotime($endDate)) {
            
            } else {
                //处理时间包含开始时间和结束时间
                $startDateStr = strtotime($startDate) - 1;
                $endDateStr = strtotime($endDate) + (24 * 3600 - 1);
                $startDate = date("Y-m-d H:i:s", $startDateStr);
                $endDate = date("Y-m-d H:i:s", $endDateStr);
                $query->whereBetween('process_date', [$startDate, $endDate]);
            }
        } else {
            $query->where('process_date', date('Y-m-d'));
        }
        
        //按区域进行查询
        if ($request->area != "" ) {
            $query->where('province_id', $request->area);
        }
        $pageNum = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 10;
        $page = $pageNum - 1;
        if ($page != 0) {
            $page = $limit * $page;
        }
        $query1 = clone $query;
        $list = $query
            ->select(
                array(
                    \DB::raw('process_date'),
                    \DB::raw('SUM(register_num) as register_num'),
                    \DB::raw('SUM(login_num) as login_nums'),
                    \DB::raw('SUM(index_num) as index_num'),
                    \DB::raw('SUM(scan_num) as scan_num'),
                    \DB::raw('SUM(detail_num) as detail_num'),
                    \DB::raw('SUM(period_num) as period_num'),
                    \DB::raw('SUM(deduction_num) as deduction_num'),
                    \DB::raw('SUM(submit_lease_num) as submit_lease_num'),
                    \DB::raw('SUM(mylease_num) as mylease_num'),
                    \DB::raw('SUM(business_num) as business_num'),
                    \DB::raw('SUM(topay_num) as topay_num'),
                    \DB::raw('SUM(dopay_num) as dopay_num'),
                    \DB::raw('SUM(pay_num) as pay_nums'),
                    \DB::raw('SUM(pay_num)/SUM(register_num) as divpay_num'),
                )
            )
            ->groupBy('process_date')
            ->offset($page)
            ->limit($limit)
            ->get()
            ->toArray();
        $count = $query1->select('id')->groupBy('process_date')->get()->count();
        $data['list'] = $list;
        $data['count'] = $count;
        return $data;
    }
    
    
    //获取续租饼图数据
    public function getHistogram($request, $type = "lists")
    {
        /// 默认查询最近一周
        $datetime = request('datetime');
        $time_type = isset($request->time_type) ? $request->time_type : 1;
        if ($time_type) {
            $time = selectTimeRange($time_type);
            if ($time) {
                $defaultDay = [
                    "begin" => $time['start_time'],
                    "end" => $time['end_time']
                ];
            } else {
                $time_s_d = explode(' - ', $datetime);
                $defaultDay = [
                    "begin" => $time_s_d[0],
                    "end" => $time_s_d[1]
                ];
            }
        }
        $returndata = [];
        $province_id = $request->province_id;
        //查询合约到期
        $sql = LeaseContract::where('contract_expired_at', '>=', $defaultDay['begin'])
            ->where('contract_expired_at', '<=', $defaultDay['end']);
        if (intval($province_id) !== 0) {
            $sql->where('province_id', $province_id);
        }
        $data = $sql->select('id')->count();
        $returndata['total_expired_at'] = $data;
        //续租
        $list = LeaseContract::where('contract_expired_at', '>=', $defaultDay['begin'])
            ->where('contract_expired_at', '<=', $defaultDay['end'])
            ->whereIn('status', ['3', '7', '8']);
        if (intval($province_id) !== 0) {
            $list->where('province_id', $province_id);
        }
        $listdata = $list->select('id')->count();
        $returndata['renewal'] = $listdata;
        
        //退租
        $listretirenum = LeaseContract::where('contract_expired_at', '>=', $defaultDay['begin'])
            ->where('contract_expired_at', '<=', $defaultDay['end'])
            ->where('status', 4);
        
        if (intval($province_id) !== 0) {
            $listretirenum->where('province_id', $province_id);
        }
        $listretirenumdata = $listretirenum->select('id')->count();
        $returndata['retirenum'] = $listretirenumdata;
        //到期30天未处理
        $returndata['other30num'] = $returndata['total_expired_at'] - $returndata['renewal'] - $returndata['retirenum'];
        
        return $returndata;
    }
    
    //获取租赁渠道列表
    public function getChannelLists($request, $type = "lists")
    {
        $query = DB::table($this->table_lease_channels);
        //按指定时间进行查询
        if ($request->datetime != "") {
            $datatime = $request->datetime;
            $floatStartDate = substr($datatime, 0, 10);
            $startDate = $floatStartDate . " 00:00:00";
            $floatEndDate = substr($datatime, -10);
            $endDate = $floatEndDate . " 00:00:00";
            if (strtotime($startDate) > time()) {
            
            } elseif (strtotime($startDate) > strtotime($endDate)) {
            
            } else {
                //处理时间包含开始时间和结束时间
                $startDateStr = strtotime($startDate) - 1;
                $endDateStr = strtotime($endDate) + (24 * 3600 - 1);
                $startDate = date("Y-m-d H:i:s", $startDateStr);
                $endDate = date("Y-m-d H:i:s", $endDateStr);
                $query->whereBetween('process_date', [$startDate, $endDate]);
            }
        }
        //按区域进行查询
        if ($request->province_id != "") {
            $query->where('province_id', $request->province_id);
        }
        //按渠道进行查询
        if ($request->channelname != "") {
            $query->where('systemtype', $request->channelname);
        }
       
        $pageNum = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 10;
        $page = $pageNum - 1;
        if ($page != 0) {
            $page = $limit * $page;
        }
        $query1 = clone $query;
        $list = objectToArray($query
            ->select(
                array(
                    \DB::raw('process_date'),
                    \DB::raw('systemtype'),
                    \DB::raw('register_num'),
                    \DB::raw('login_num'),
                    \DB::raw('index_num'),
                    \DB::raw('scan_num'),
                    \DB::raw('detail_num'),
                    \DB::raw('period_num'),
                    \DB::raw('deduction_num'),
                    \DB::raw('submit_lease_num'),
                    \DB::raw('mylease_num'),
                    \DB::raw('business_num'),
                    \DB::raw('topay_num'),
                    \DB::raw('dopay_num'),
                    \DB::raw('pay_num'),
                    \DB::raw('pay_num/register_num as divpay_num'),
                )
            )
            ->offset($page)
            ->limit($limit)
            ->orderByDesc('process_date')
            ->get()
            ->toArray());
        $count = $query1->select('id')->get()->count();
        $data['list'] = $list;
        $data['count'] = $count;
        return $data;
    }
    
    //获取续租列表
    public function getHistogramLists($request, $type = "lists")
    {
        $effectContractStatus = [3, 4, 5, 6, 7, 8];
        $query = DB::table($this->table_lease_contracts)->whereIn('status', $effectContractStatus);
        //按指定时间进行查询
        if ($request->datetime != "") {
            $datatime = $request->datetime;
            $floatStartDate = substr($datatime, 0, 10);
            $startDate = $floatStartDate . " 00:00:00";
            $floatEndDate = substr($datatime, -10);
            $endDate = $floatEndDate . " 00:00:00";
            if (strtotime($startDate) > time()) {
            
            } elseif (strtotime($startDate) > strtotime($endDate)) {
            
            } else {
                //处理时间包含开始时间和结束时间
                $startDateStr = strtotime($startDate) - 1;
                $endDateStr = strtotime($endDate) + (24 * 3600 - 1);
                $startDate = date("Y-m-d H:i:s", $startDateStr);
                $endDate = date("Y-m-d H:i:s", $endDateStr);
                $query->whereBetween('contract_expired_at', [$startDate, $endDate]);
            }
        } else {
            $query->where('contract_expired_at', '<', date("Y-m-d"));
        }
        //按区域进行查询
        if ($request->province_id != "" && intval($request->province_id) != 0) {
            $query->where('province_id', $request->province_id);
        }
        $pageNum = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 10;
        $page = $pageNum - 1;
        if ($page != 0) {
            $page = $limit * $page;
        }
        $query1 = clone $query;
        $list = objectToArray($query
            ->select(
                array(
                    \DB::raw('contract_expired_at'),
                    \DB::raw('count(id) as id_counts'),
                    \DB::raw('status'),
                )
            )
            ->groupBy('contract_expired_at')
            ->offset($page)
            ->limit($limit)
            ->orderByDesc('contract_expired_at')
            ->get()
            ->toArray());
        foreach ($list as $key => $value) {
            //所有单子中续租的
            $count1 = DB::table($this->table_lease_contracts)->whereIn('status',
                ['3', '7', '8'])->where('contract_expired_at',
                $value['contract_expired_at'])->count();
            //所有单子中退租的
            $count2 = DB::table($this->table_lease_contracts)->where('status', '4')->where('contract_expired_at',
                $value['contract_expired_at'])->count();
            $list[$key]['renewal'] = $count1;
            $list[$key]['retirenum'] = $count2;
            $list[$key]['other30num'] = $value['id_counts'] - $count1 - $count2;
        }
        $count = $query1->select('id')->groupBy('contract_expired_at')->get()->count();
        $data['list'] = $list;
        $data['count'] = $count;
        return $data;
    }
    
    
    //获取 租赁数量对比   折线图
    public function getnewoldlists($request)
    {
        $defaultDay = [
            "begin" => date("Y-m-d", strtotime("-6 day")),
            "end" => date("Y-m-d")
        ];
        $renewal_date = request('renewal_date');
        $time_type = isset($request->time_type) ? $request->time_type : 1;
        if ($time_type) {
            $time = selectTimeRange($time_type);
            if ($time) {
                $defaultDay = [
                    "begin" => $time['start_time'],
                    "end" => $time['end_time']
                ];
            } else {
                $time_s_d = explode(' - ', $renewal_date);
                $defaultDay = [
                    "begin" => $time_s_d[0],
                    "end" => $time_s_d[1]
                ];
            }
        }
        
        //新用户
        if ($request->type) {
            request()->offsetSet('type', '1');
        }
        $newdata = LeaseContractDateInfo::leaseTrend($request, $defaultDay);
        $newnumData = [];
        $actuallyBegin = isset($newdata[0]) ? $newdata[0]["date"] : $defaultDay["begin"];
        foreach ($newdata as $datum) {
            $newnumData[] = $datum["today_num"];
        }
        //老用户
        if ($request->type) {
            request()->offsetSet('type', '2');
        }
        $data = LeaseContractDateInfo::leaseTrend($request, $defaultDay);
        $numData = [];
        $actuallyBegin = isset($data[0]) ? $data[0]["date"] : $defaultDay["begin"];
        //出现四条数据
        foreach ($data as $datum) {
            $numData[] = $datum["today_num"];
        }
        $returnDatabefore = [
            "dayArr" => getDateRange($actuallyBegin, $defaultDay["end"]),
        ];
        $series = [];
        $linesarr = ['新用户', '老用户'];
        $totalnumData = [$newnumData, $numData];
        
        
        foreach ($linesarr as $k => $lines) {
            $series[$k] = [
                "name" => $lines,
                "type" => 'line',
                "stack" => $lines,
                "symbolSize" => 8,
                "symbol" => 'circle',
                "data" => $totalnumData[$k]
            ];
        }
        $returnData = [
            "linedays" => $linesarr,
            "dayArr" => $returnDatabefore['dayArr'],
            "series" => $series,
        ];
        return $returnData;
    }
    
    //获取 租赁数量对比   折线图
    public function getNewOldTotalLists($request)
    {
       
        $renewal_date = request('renewal_date');
        $time_type = isset($request->time_type) ? $request->time_type : 1;
        if ($time_type) {
            $time = selectTimeRange($time_type);
            if ($time) {
                $defaultDay = [
                    "begin" => $time['start_time'],
                    "end" => $time['end_time']
                ];
            } else {
                $time_s_d = explode(' - ', $renewal_date);
                $defaultDay = [
                    "begin" => $time_s_d[0],
                    "end" => $time_s_d[1]
                ];
            }
        }
        
        //新用户
        if ($request->type) {
            request()->offsetSet('type', '1');
        }
        $newdata = LeaseContractDateInfo::leaseTrend($request, $defaultDay);
        $newnumData = [];
        $actuallyBegin = isset($newdata[0]) ? $newdata[0]["date"] : $defaultDay["begin"];
        foreach ($newdata as $datum) {
            $newnumData[] = $datum["today_num"];
        }
        //老用户
        if ($request->type) {
            request()->offsetSet('type', '2');
        }
        $data = LeaseContractDateInfo::leaseTrend($request, $defaultDay);
        $numData = [];
        $actuallyBegin = isset($data[0]) ? $data[0]["date"] : $defaultDay["begin"];
        //出现四条数据
        foreach ($data as $datum) {
            $numData[] = $datum["today_num"];
        }
        $returnDatabefore = [
            "dayArr" => getDateRange($actuallyBegin, $defaultDay["end"]),
        ];
        $series = [];
        $linesarr = ['用户'];
        
        $totalnumData = [$newnumData, $numData];
        $totalnumData = comm_sumarrss($totalnumData);
    
        foreach ($linesarr as $k => $lines) {
            $series[$k] = [
                "name" => $lines,
                "type" => 'line',
                "stack" => $lines,
                "symbolSize" => 8,
                "symbol" => 'circle',
                "data" => $totalnumData
            ];
        }
        $returnData = [
            "linedays" => $linesarr,
            "dayArr" => $returnDatabefore['dayArr'],
            "series" => $series,
        ];
        return $returnData;
    }
    
    //获取 租赁金额对比  折线图
    public function getnewoldmoneylists($request)
    {
        $req = $request->all();
        $renewal_date = request('renewal_date');
        $time_type = isset($request->time_type) ? $request->time_type : 1;
        if ($time_type) {
            $time = selectTimeRange($time_type);
            if ($time) {
                $defaultDay = [
                    "begin" => $time['start_time'],
                    "end" => $time['end_time']
                ];
            } else {
                $time_s_d = explode(' - ', $renewal_date);
                $defaultDay = [
                    "begin" => $time_s_d[0],
                    "end" => $time_s_d[1]
                ];
            }
        }
        $defaultDaybegin = substr($defaultDay['begin'], 0, 10);
        $defaultDayend = substr($defaultDay['end'], 0, 10);
        $defaultDayend =  date("Y-m-d",strtotime("+1 day",strtotime("$defaultDayend")));
        
        $whereSql = "";
        if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
            $whereSql = " and province_id = " . $req["agentId"];
        }
        $sql = " SELECT today_rental,date"
            . "  FROM lease_contract_date_infos "
            . " where type = 1 $whereSql  and "
            . " date >= '$defaultDaybegin' and date <=  '$defaultDayend'";
        $data = DB::select($sql);
        $newnumData = [];
        //新用户租赁金额数据
        if (!empty($data)) {
            foreach ($data as $datum) {
                $newnumData[] = $datum->today_rental;
            }
        }
        $sql1 = " SELECT today_rental,date"
            . "  FROM lease_contract_date_infos "
            . " where type = 2 $whereSql  and "
            . " date >= '$defaultDaybegin' and date <=  '$defaultDayend'";
        $olduserdata = DB::select($sql1);
        $oldnumData = [];
        //老用户租赁金额数据
        if (!empty($olduserdata)) {
            foreach ($olduserdata as $datum) {
                $oldnumData[] = $datum->today_rental;
            }
        }
        $totalnumData = [$newnumData, $oldnumData];
        $linesarr = ['新用户', '老用户'];
        $series = [];
        foreach ($linesarr as $k => $lines) {
            $series[$k] = [
                "name" => $lines,
                "type" => 'line',
                "stack" => $lines,
                "symbolSize" => 8,
                "symbol" => 'circle',
                "data" => $totalnumData[$k]
            ];
        }
        $returnDatabefore = [
            "dayArr" => getDateRange($defaultDay["begin"], $defaultDay["end"]),
        ];
        
        $returnData = [
            "linedays" => $linesarr,
            "dayArr" => $returnDatabefore['dayArr'],
            "series" => $series,
        ];
        return $returnData;
    }
    
    //注册-租赁发起周期  ---    统计表
    public function getPeriodlist($request)
    {
        $query = DB::table($this->table_register_period);
        //按指定时间进行查询
        if ($request->datetime != "") {
            $datatime = $request->datetime;
            $floatStartDate = substr($datatime, 0, 10);
            $startDate = $floatStartDate . " 00:00:00";
            $floatEndDate = substr($datatime, -10);
            $endDate = $floatEndDate . " 00:00:00";
            if (strtotime($startDate) > time()) {
            
            } elseif (strtotime($startDate) > strtotime($endDate)) {
            
            } else {
                //处理时间包含开始时间和结束时间
                $startDateStr = strtotime($startDate) - 1;
                $endDateStr = strtotime($endDate) + (24 * 3600 - 1);
                $startDate = date("Y-m-d H:i:s", $startDateStr);
                $endDate = date("Y-m-d H:i:s", $endDateStr);
                $query->whereBetween('register_date', [$startDate, $endDate]);
            }
        }
        //按区域进行查询
        if ($request->area != "") {
            $query->where('province_id', $request->area);
        }
        $pageNum = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 10;
        $page = $pageNum - 1;
        if ($page != 0) {
            $page = $limit * $page;
        }
        $query1 = clone $query;
        $list = $query->select(
            'register_date',
            'register_num',
            'today_num',
            'one_three_num',
            'four_seven_num',
            'eight_ten_num',
            'eleven_thirty_num',
            'thirty_no_num'
        )
            ->offset($page)
            ->limit($limit)
            ->orderByDesc('register_date')
            ->get()
            ->toArray();
        $count = $query1->select('id')->get()->count();
        $data['list'] = $list;
        $data['count'] = $count;
        return $data;
    }
}
