<?php

namespace Modules\Manage\Models;
use Illuminate\Support\Facades\DB;

class LeaseRentRebate extends Model
{
    protected $table="lease_rent_rebate";

    //获取退租列表
    public function  getLists($request, $type = "lists")
    {


        $pageNum = isset($request->page) ? $request->page : 0;

        $limit = isset($request->limit) ? $request->limit : 10;
        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        if(!empty($renewal_date)){
            $time = explode(' - ',$renewal_date);
            $where[] = ['rent_release_date','>=',$time[0]];
            $where[] = ['rent_release_date','<',$time[1]];
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

        $where['type'] = $type;

//        $count = self::count();
        $page=$pageNum-1;
        if ($page != 0) {
            $page = $limit * $page;
//            $limit = $limit*$pageNum;
        }

        $count = self::where($where)->count();

        $list = self::where($where)->offset($page)->limit($limit)->orderBy('rent_release_date','desc')->get()->toArray();

        $data['list'] = $list;
        $data['count'] = $count;

         return  $data;
    }


    //获取退租饼图数据
    public function getHistogram($request, $type = "lists")
    {

        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
             $time =  selectTimeRange($time_type);
             if ($time){
                 $where[] = ['rent_release_date','>=',$time['start_time']];
                 $where[] = ['rent_release_date','<',$time['end_time']];

             }else{
                 $time_s_d = explode(' - ',$renewal_date);
                 $where[] = ['rent_release_date','>=',$time_s_d[0]];
                 $where[] = ['rent_release_date','<',$time_s_d[1]];
             }
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

        $where['type'] = $type;

        $list = self::where($where)->first(
            array(
                \DB::raw('SUM(advance_rent_release) as advance_rent_release'),
                \DB::raw('SUM(expire_rent_release_num) as expire_rent_release_num'),
                \DB::raw('SUM(overtime_ten_rent_release_num) as overtime_ten_rent_release_num'),
                \DB::raw('SUM(overtime_ten_thirty_rent_release_num) as overtime_ten_thirty_rent_release_num')
            )
        )->toArray();


        return  $list;
    }


    //获取退租饼图数据
    public function getbroken($request, $type = "lists")
    {

        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
            $time =  selectTimeRange($time_type);
            if ($time){
                $where[] = ['rent_release_date','>=',$time['start_time']];
                $where[] = ['rent_release_date','<',$time['end_time']];
            }else{
                $time_s_d = explode(' - ',$renewal_date);
                $where[] = ['rent_release_date','>=',$time_s_d[0]];
                $where[] = ['rent_release_date','<',$time_s_d[1]];
            }
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }
        $where['type'] = $type;

        $list = self::where($where)->get(
            array(
                \DB::raw('DATE_FORMAT(rent_release_date,"%m-%d") as rent_release_date'),
                \DB::raw('rent_release_num'),
                \DB::raw('rent_release_amount')
            )
        )->toArray();

        $renewal_date = array_column($list, 'rent_release_date');
        $renewal_num = array_column($list, 'rent_release_num');
        $renewal_amount = array_column($list, 'rent_release_amount');

        $data['rent_release_date'] = $renewal_date;
        $data['rent_release_num'] = $renewal_num;
        $data['rent_release_amount'] = $renewal_amount;

         return  $data;
    }


    public function  getRebateRentList($request, $type = "lists")
    {
        $pageNum = isset($request->page) ? $request->page : 0;

        $limit = isset($request->limit) ? $request->limit : 10;
        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        if(!empty($renewal_date)){
            $time = explode(' - ',$renewal_date);
            $where[] = ['rent_release_date','>=',$time[0]];
            $where[] = ['rent_release_date','<',$time[1]];
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

        $where['type'] = $type;

//        $count = self::count();
        $page=$pageNum-1;
        if ($page != 0) {
            $page = $limit * $page;
//            $limit = $limit*$pageNum;
        }

        $count = app(LeaseRentRebateReport ::class)->where($where)->count();

        $list = app(LeaseRentRebateReport ::class)->select(DB::raw('IFNULL(CONCAT((convert(expire_rent_future_num/rent_release_num,decimal(10,2)))*100,"%"),"0.00%") as expire_rent_than,rent_release_date,
       rent_release_num,expire_rent_future_num,overtime_one_three_rent_future_num,overtime_four_seven_rent_future_num,overtime_eight_ten_rent_future_num,overtime_ten_thirty_rent_future_num,overtime_thirty_no_rent_future_num'))->where($where)->offset($page)->limit($limit)->orderBy('rent_release_date','desc')->get()->toArray();

        $data['list'] = $list;
        $data['count'] = $count;

        return  $data;
    }


    public function getArea($request, $type = "lists")
    {

        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
            $time =  selectTimeRange($time_type);
            if ($time){
                $where[] = ['rent_release_date','>=',$time['start_time']];
                $where[] = ['rent_release_date','<',$time['end_time']];
            }else{
                $time_s_d = explode(' - ',$renewal_date);
                $where[] = ['rent_release_date','>=',$time_s_d[0]];
                $where[] = ['rent_release_date','<',$time_s_d[1]];
            }
        }


        $where[] = ['province_id','>',0];

        $where['type'] = $type;

        $list = self::where($where)->selectRaw("sum(rent_release_amount) as rent_release_amount,province_id")
            ->groupBy("province_id")->pluck('rent_release_amount','province_id')->toArray();

        $renewal_num = self::where($where)->selectRaw("sum(rent_release_num) as rent_release_num,province_id")
            ->groupBy("province_id")->pluck('rent_release_num','province_id')->toArray();

//        $data = LeaseRenewalReport::where($where)->selectRaw("sum(expire_rent_num) as expire_rent_num,province_id")
//            ->groupBy("province_id")->pluck('expire_rent_num','province_id')->toArray();
        $provinces = allLeaseProvinces();

        foreach ($provinces as $key=>$value){

            $return_data['renewal_amount'][] = isset($list[$key])?$list[$key]:0;
            $return_data['renewal_num'][] = isset($renewal_num[$key])?$renewal_num[$key]:0;
//            $return_data['expire_rent_num'][] = isset($data[$key])?$data[$key]:0;
            $return_data['province_name'][] = $value;


        }

        return  $return_data;
    }

}
