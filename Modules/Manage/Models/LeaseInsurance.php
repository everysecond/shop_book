<?php

namespace Modules\Manage\Models;
use Illuminate\Support\Facades\DB;

class LeaseInsurance extends Model
{

    protected $table="lease_insurance";

    //获取投保列表
    public function getLists($request, $type = "lists")
    {

        $pageNum = isset($request->page) ? $request->page : 0;

        $limit = isset($request->limit) ? $request->limit : 10;
        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        if(!empty($renewal_date)){
            $time = explode(' - ',$renewal_date);
            $where[] = ['rent_date','>=',$time[0]];
            $where[] = ['rent_date','<',$time[1]];
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

        $where['type'] = $type;

        $count = self::count();
        $page=$pageNum-1;
        if ($page != 0) {
            $page = $limit * $page;
//            $limit = $limit*$pageNum;
        }

        $count = self::where($where)->count();

        $list = self::select(DB::raw('CONCAT((ROUND(insure_num/(insure_num+uninsured_num)*100,2)),"%") as insure_rent,rent_date,
        insure_num,rent_num,uninsured_num,renewal_num,renewal_insure_num,rent_insure_num'))->where($where)->offset($page)
            ->limit($limit)->orderBy('rent_date','desc')->get()->toArray();

        $data['list'] = $list;
        $data['count'] = $count;

         return  $data;
    }


    //获取投保饼图数据
    public function getHistogram($request, $type = "lists")
    {

        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
             $time =  selectTimeRange($time_type);
             if ($time){
                 $where[] = ['rent_date','>=',$time['start_time']];
                 $where[] = ['rent_date','<',$time['end_time']];

             }else{
                 $time_s_d = explode(' - ',$renewal_date);
                 $where[] = ['rent_date','>=',$time_s_d[0]];
                 $where[] = ['rent_date','<',$time_s_d[1]];
             }
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

        $where['type'] = $type;

        $list = self::where($where)->first(
            array(
                \DB::raw('SUM(uninsured_num) as uninsured_num'),
                \DB::raw('SUM(insure_num) as insure_num'),

            )
        )->toArray();


        return  $list;
    }

    //电池报失柱状图数据
    public function getColumnar($request, $type = "lists")
    {

        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
            $time =  selectTimeRange($time_type);
            if ($time){
                $where[] = ['rent_date','>=',$time['start_time']];
                $where[] = ['rent_date','<',$time['end_time']];
            }else{
                $time_s_d = explode(' - ',$renewal_date);
                $where[] = ['rent_date','>=',$time_s_d[0]];
                $where[] = ['rent_date','<',$time_s_d[1]];
            }
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }
        $where['type'] = $type;

        $list = self::where($where)->get(
            array(
                \DB::raw('DATE_FORMAT(rent_date,"%m-%d") as rent_date'),
                \DB::raw('report_loss_num')
            )
        )->toArray();

        $renewal_date = array_column($list, 'rent_date');
        $renewal_num = array_column($list, 'report_loss_num');

        $data['rent_date'] = $renewal_date;
        $data['report_loss_num'] = $renewal_num;


         return  $data;
    }


    //获取租赁报失数统计
    public function getRentLoss($request, $type = "lists")
    {

        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
            $time =  selectTimeRange($time_type);
            if ($time){
                $where[] = ['rent_date','>=',$time['start_time']];
                $where[] = ['rent_date','<',$time['end_time']];

            }else{
                $time_s_d = explode(' - ',$renewal_date);
                $where[] = ['rent_date','>=',$time_s_d[0]];
                $where[] = ['rent_date','<',$time_s_d[1]];
            }
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

        $where['type'] = $type;

        $list = self::where($where)->first(
            array(
                \DB::raw('SUM(report_loss_num) as report_loss_num'),
                \DB::raw('SUM(rent_num) as rent_num'),

            )
        )->toArray();


        return  $list;
    }


    //获取投保报失数统计
    public function getInsureLoss($request, $type = "lists")
    {

        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
            $time =  selectTimeRange($time_type);
            if ($time){
                $where[] = ['rent_date','>=',$time['start_time']];
                $where[] = ['rent_date','<',$time['end_time']];

            }else{
                $time_s_d = explode(' - ',$renewal_date);
                $where[] = ['rent_date','>=',$time_s_d[0]];
                $where[] = ['rent_date','<',$time_s_d[1]];
            }
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

        $where['type'] = $type;

        $list = self::where($where)->first(
            array(
                \DB::raw('SUM(report_loss_num) as report_loss_num'),
                \DB::raw('SUM(insure_num) as insure_num'),

            )
        )->toArray();


        return  $list;
    }


    //获取续租饼图数据
    public function  getbroken($request, $type = "lists")
    {

        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
            $time =  selectTimeRange($time_type);
            if ($time){
                $where[] = ['rent_date','>=',$time['start_time']];
                $where[] = ['rent_date','<',$time['end_time']];
            }else{
                $time_s_d = explode(' - ',$renewal_date);
                $where[] = ['rent_date','>=',$time_s_d[0]];
                $where[] = ['rent_date','<',$time_s_d[1]];
            }
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }
        $where['type'] = $type;

        $list = self::where($where)->get(
            array(
                \DB::raw('DATE_FORMAT(rent_date,"%m-%d") as rent_date'),
                \DB::raw('insure_num'),

            )
        )->toArray();

        $renewal_date = array_column($list, 'rent_date');
        $renewal_num = array_column($list, 'insure_num');
//        $renewal_amount = array_column($list, 'renewal_amount');

        $data['renewal_date'] = $renewal_date;
        $data['renewal_num'] = $renewal_num;
//        $data['renewal_amount'] = $renewal_amount;

        return  $data;
    }


    //获取投保列表
    public function getLists1($request, $type = "lists")
    {

        $pageNum = isset($request->page) ? $request->page : 0;

        $limit = isset($request->limit) ? $request->limit : 10;
        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        if(!empty($renewal_date)){
            $time = explode(' - ',$renewal_date);
            $where[] = ['rent_date','>=',$time[0]];
            $where[] = ['rent_date','<',$time[1]];
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

        $where['type'] = $type;

        $count = self::count();
        $page=$pageNum-1;
        if ($page != 0) {
            $page = $limit * $page;
//            $limit = $limit*$pageNum;
        }

        $count = self::where($where)->count();

        $list = self::select(DB::raw('rent_date,report_loss_num,report_loss_user_num'))
            ->where($where)->offset($page)
            ->limit($limit)->orderBy('rent_date','desc')->get()->toArray();

        $data['list'] = $list;
        $data['count'] = $count;

        return  $data;
    }

}
