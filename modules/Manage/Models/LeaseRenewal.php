<?php

namespace Modules\Manage\Models;
use Illuminate\Support\Facades\DB;
use Modules\Lease\Models\BlLeasePayment;

class LeaseRenewal extends Model
{
    protected $table="lease_renewal";

    //获取续租列表
    public function  getLists($request, $type = "lists")
    {


        $pageNum = isset($request->page) ? $request->page : 0;

        $limit = isset($request->limit) ? $request->limit : 10;
        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        if(!empty($renewal_date)){
            $time = explode(' - ',$renewal_date);
            $where[] = ['renewal_date','>=',$time[0]];
            $where[] = ['renewal_date','<',$time[1]];
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

        $where['type'] = $type;

//        $count = self::count();
        $page=$pageNum-1;
        if ($page != 0) {
            $page = $limit * $page;
            //$limit = $limit*$pageNum;
        }

        $count = self::where($where)->count();

        $list = self::select(DB::raw('convert(renewal_amount/renewal_num,decimal(10,2)) as renewal_average,renewal_date,
        renewal_num,renewal_user_num,renewal_num,advance_renewal,expire_renewal_num,overtime_ten_renewal_num,expire_unrent_num,
        overtime_ten_thirty_renewal_num,renewal_amount'))->where($where)->offset($page)->limit($limit)->orderBy('renewal_date','desc')->get()->toArray();

        $data['list'] = $list;
        $data['count'] = $count;

         return  $data;
    }


    //获取续租饼图数据
    public function getHistogram($request, $type = "lists")
    {

        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
             $time =  selectTimeRange($time_type);
             if ($time){
                 $where[] = ['renewal_date','>=',$time['start_time']];
                 $where[] = ['renewal_date','<',$time['end_time']];

             }else{
                 $time_s_d = explode(' - ',$renewal_date);
                 $where[] = ['renewal_date','>=',$time_s_d[0]];
                 $where[] = ['renewal_date','<',$time_s_d[1]];
             }
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

        $where['type'] = $type;

        $list = self::where($where)->first(
            array(
                \DB::raw('SUM(advance_renewal) as advance_renewal'),
                \DB::raw('SUM(expire_renewal_num) as expire_renewal_num'),
                \DB::raw('SUM(overtime_ten_renewal_num) as overtime_ten_renewal_num'),
                \DB::raw('SUM(overtime_ten_thirty_renewal_num) as overtime_ten_thirty_renewal_num')
            )
        )->toArray();


        return  $list;
    }


    //获取续租饼图数据
    public function getbroken($request, $type = "lists")
    {

        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
            $time =  selectTimeRange($time_type);
            if ($time){
                $where[] = ['renewal_date','>=',$time['start_time']];
                $where[] = ['renewal_date','<',$time['end_time']];
            }else{
                $time_s_d = explode(' - ',$renewal_date);
                $where[] = ['renewal_date','>=',$time_s_d[0]];
                $where[] = ['renewal_date','<',$time_s_d[1]];
            }
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }
        $where['type'] = $type;

        $list = self::where($where)->get(
            array(
                \DB::raw('DATE_FORMAT(renewal_date,"%m-%d") as renewal_date'),
                \DB::raw('renewal_num'),
                \DB::raw('renewal_amount')
            )
        )->toArray();

        $renewal_date = array_column($list, 'renewal_date');
        $renewal_num = array_column($list, 'renewal_num');
        $renewal_amount = array_column($list, 'renewal_amount');

        $data['renewal_date'] = $renewal_date;
        $data['renewal_num'] = $renewal_num;
        $data['renewal_amount'] = $renewal_amount;

         return  $data;
    }


    public function  getRenewalCustomerList($request, $type = "lists")
    {
        $pageNum = isset($request->page) ? $request->page : 0;

        $limit = isset($request->limit) ? $request->limit : 10;
        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        if(!empty($renewal_date)){
            $time = explode(' - ',$renewal_date);
            $where[] = ['renewal_date','>=',$time[0]];
            $where[] = ['renewal_date','<',$time[1]];
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

        $where['type'] = $type;

//        $count = self::count();
        $page=$pageNum-1;
        if ($page != 0) {
            $page = $limit * $page;
            //$limit = $limit*$pageNum;
        }

        $count = self::where($where)->count();

        $list = self::select(DB::raw('IFNULL(CONCAT((convert(advance_renewal/renewal_num,decimal(10,2)))*100,"%"),"0.00%") as advance_renewal_than,renewal_date,
        CONCAT((convert(expire_renewal_num/renewal_num,decimal(10,2)))*100,"%") as expire_renewal_than,renewal_num,renewal_user_num,
        advance_renewal,expire_renewal_num,overtime_ten_renewal_num,overtime_ten_thirty_renewal_num,renewal_amount,overtime_one_three_renewal_num,
        overtime_four_seven_renewal_num,overtime_eight_ten_renewal_num'))->where($where)->offset($page)->limit($limit)->orderBy('renewal_date','desc')->get()->toArray();

        $data['list'] = $list;
        $data['count'] = $count;

        return  $data;
    }



    public function  getExpireRenewalList($request, $type = "lists")
    {
        $pageNum = isset($request->page) ? $request->page : 0;

        $limit = isset($request->limit) ? $request->limit : 10;
        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        if(!empty($renewal_date)){
            $time = explode(' - ',$renewal_date);
            $where[] = ['renewal_date','>=',$time[0]];
            $where[] = ['renewal_date','<',$time[1]];
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

        $where['type'] = $type;

        $page=$pageNum-1;
        if ($page != 0) {
            $page = $limit * $page;
//            $limit = $limit*$pageNum;
        }

        $count = app(LeaseRenewalReport ::class)->where($where)->count();

        $list = app(LeaseRenewalReport ::class)->select(DB::raw('IFNULL(CONCAT((convert(expire_renewal_num/expire_rent_num,decimal(10,2)))*100,"%"),"0.00%") as expire_rent_renewal_than,renewal_date,
        expire_rent_num,expire_renewal_num,overtime_one_three_renewal_future_num,overtime_four_seven_renewal_future_num,overtime_eight_ten_renewal_future_num,
        overtime_ten_thirty_renewal_future_num,overtime_thirty_no_renewal_future_num'))->where($where)->offset($page)->limit($limit)->orderBy('renewal_date','desc')->get()->toArray();

        $data['list'] = $list;
        $data['count'] = $count;

        return  $data;
    }

    //获取续租饼图数据
    public function getArea($request, $type = "lists")
    {

        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
            $time =  selectTimeRange($time_type);
            if ($time){
                $where[] = ['renewal_date','>=',$time['start_time']];
                $where[] = ['renewal_date','<',$time['end_time']];
            }else{
                $time_s_d = explode(' - ',$renewal_date);
                $where[] = ['renewal_date','>=',$time_s_d[0]];
                $where[] = ['renewal_date','<',$time_s_d[1]];
            }
        }


        $where[] = ['province_id','>',0];

        $where['type'] = $type;

        $list = self::where($where)->selectRaw("sum(renewal_amount) as renewal_amount,province_id")
            ->groupBy("province_id")->pluck('renewal_amount','province_id')->toArray();

        $renewal_num = self::where($where)->selectRaw("sum(renewal_num) as renewal_num,province_id")
            ->groupBy("province_id")->pluck('renewal_num','province_id')->toArray();

        $data = LeaseRenewalReport::where($where)->selectRaw("sum(expire_rent_num) as expire_rent_num,province_id")
            ->groupBy("province_id")->pluck('expire_rent_num','province_id')->toArray();
        $provinces = allLeaseProvinces();

        foreach ($provinces as $key=>$value){

            $return_data['renewal_amount'][] = isset($list[$key])?$list[$key]:0;
            $return_data['renewal_num'][] = isset($renewal_num[$key])?$renewal_num[$key]:0;
            $return_data['expire_rent_num'][] = isset($data[$key])?$data[$key]:0;
            $return_data['province_name'][] = $value;


        }

        return  $return_data;
    }


    //获取续租饼图数据
    public function advanceRenewal($request, $type = "lists")
    {

        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
            $time =  selectTimeRange($time_type);
            if ($time){
                $where[] = ['renewal_date','>=',$time['start_time']];
                $where[] = ['renewal_date','<',$time['end_time']];

            }else{
                $time_s_d = explode(' - ',$renewal_date);
                $where[] = ['renewal_date','>=',$time_s_d[0]];
                $where[] = ['renewal_date','<',$time_s_d[1]];
            }
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

        $where['type'] = $type;

        $list =  app(LeaseAdvanceRenewal::class)->where($where)->first(
            array(
                \DB::raw('SUM(advance_one_five_renewal_num) as advance_one_five_renewal_num'),
                \DB::raw('SUM(advance_six_ten_renewal_num) as advance_six_ten_renewal_num'),
                \DB::raw('SUM(advance_ten_thirty_renewal_num) as advance_ten_thirty_renewal_num'),
                \DB::raw('SUM(advance_over_thirty_renewal_num) as advance_over_thirty_renewal_num')
            )
        )->toArray();


        return  $list;
    }



}
