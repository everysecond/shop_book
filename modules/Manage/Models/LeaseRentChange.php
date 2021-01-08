<?php

namespace Modules\Manage\Models;
use Illuminate\Support\Facades\DB;

class LeaseRentChange extends Model
{
    protected $table="lease_rent_change";

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
            $where[] = ['rent_change_date','>=',$time[0]];
            $where[] = ['rent_change_date','<',$time[1]];
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

        $list = self::where($where)->offset($page)->limit($limit)->orderBy('rent_change_date','desc')->get()->toArray();

        $data['list'] = $list;
        $data['count'] = $count;

         return  $data;
    }


    //获取换租饼图数据
    public function getHistogram($request, $type = "lists")
    {

        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
             $time =  selectTimeRange($time_type);
             if ($time){
                 $where[] = ['change_reason_date','>=',$time['start_time']];
                 $where[] = ['change_reason_date','<',$time['end_time']];

             }else{
                 $time_s_d = explode(' - ',$renewal_date);
                 $where[] = ['change_reason_date','>=',$time_s_d[0]];
                 $where[] = ['change_reason_date','<',$time_s_d[1]];
             }
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }

//        $where['type'] = $type;

        $list = DB::table('lease_change_reason')->selectRaw("sum(change_reason_num) as sum,type")->where($where)->groupBy("type")
            ->pluck('sum','type')->toArray();
        $data = [];
        foreach ($list as $key=>$value){
            if ($key == 1){
                $data['leakage'] = $value;
            }
            if ($key == 2){
                $data['close_distance'] = $value;
            }
            if ($key == 3){
                $data['swelling'] = $value;
            }
        }

        return  $data;
    }


    //获取换租折现图数据
    public function getbroken($request, $type = "lists")
    {

        $province_id = request('province_id');
        $type = isset($request->type) ? $request->type :1;
        $renewal_date = request('renewal_date');

        $time_type = isset($request->time_type) ? $request->time_type :1;

        if ($time_type){
            $time =  selectTimeRange($time_type);
            if ($time){
                $where[] = ['rent_change_date','>=',$time['start_time']];
                $where[] = ['rent_change_date','<',$time['end_time']];
            }else{
                $time_s_d = explode(' - ',$renewal_date);
                $where[] = ['rent_change_date','>=',$time_s_d[0]];
                $where[] = ['rent_change_date','<',$time_s_d[1]];
            }
        }

        if ($province_id != '') {
            $where['province_id'] = $province_id;
        }
        $where['type'] = $type;

        $list = self::where($where)->get(
            array(
                \DB::raw('DATE_FORMAT(rent_change_date,"%m-%d") as rent_change_date'),
                \DB::raw('rent_change_num')
            )
        )->toArray();

        $renewal_date = array_column($list, 'rent_change_date');
        $renewal_num = array_column($list, 'rent_change_num');

        $data['rent_change_date'] = $renewal_date;
        $data['rent_change_num'] = $renewal_num;


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
                $where[] = ['rent_change_date','>=',$time['start_time']];
                $where[] = ['rent_change_date','<',$time['end_time']];
            }else{
                $time_s_d = explode(' - ',$renewal_date);
                $where[] = ['rent_change_date','>=',$time_s_d[0]];
                $where[] = ['rent_change_date','<',$time_s_d[1]];
            }
        }


        $where[] = ['province_id','>',0];

        $where['type'] = $type;

//        $list = self::where($where)->selectRaw("sum(rent_release_amount) as rent_release_amount,province_id")
//            ->groupBy("province_id")->pluck('rent_release_amount','province_id')->toArray();

        $renewal_num = self::where($where)->selectRaw("sum(rent_change_num) as rent_change_num,province_id")
            ->groupBy("province_id")->pluck('rent_change_num','province_id')->toArray();

//        $data = LeaseRenewalReport::where($where)->selectRaw("sum(expire_rent_num) as expire_rent_num,province_id")
//            ->groupBy("province_id")->pluck('expire_rent_num','province_id')->toArray();
        $provinces = allLeaseProvinces();

        foreach ($provinces as $key=>$value){

//            $return_data['renewal_amount'][] = isset($list[$key])?$list[$key]:0;
            $return_data['renewal_num'][] = isset($renewal_num[$key])?$renewal_num[$key]:0;
//            $return_data['expire_rent_num'][] = isset($data[$key])?$data[$key]:0;
            $return_data['province_name'][] = $value;


        }

        return  $return_data;
    }

}
