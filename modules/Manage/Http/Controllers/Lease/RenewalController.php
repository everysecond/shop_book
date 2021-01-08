<?php

namespace Modules\Manage\Http\Controllers\Lease;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Kood\Models\depot;
use Modules\Kood\Models\KdSaleOrder;
use Modules\Kood\Models\RecycleOrder;
use Modules\Kood\Models\SaleOrder;
use Modules\Kood\Models\Sites;
use Modules\Kood\Models\User;
use Modules\Lease\Models\BlLeaseContract;
use Modules\Lease\Models\BlLeasePayment;
use Modules\Lease\Models\BlUserInsurance;
use Modules\Manage\Models\LeaseRenewal;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseContractDateInfo;
use Modules\Manage\Models\Report\LeasePayment;
use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Report\LeaseUser;
use Modules\Manage\Models\Service\LeaseIncomeLog;

class RenewalController extends Controller
{
    protected $LeaseRenewal;

    public function __construct(LeaseRenewal $LeaseRenewal)
    {
        $this->LeaseRenewal = $LeaseRenewal;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('manage::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('manage::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show(Request $request)
    {
        $provinces = allUserProvinces();
        $timeType = timeType();
        return view('manage::lease.report.renewal.show',compact("provinces","timeType"));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('manage::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }


    //续租列表搜索
    public function search(Request $request)
    {
        $list = $this->LeaseRenewal->getLists($request, "lists");
        $data = $list['list'];
        $count = $list['count'];

        return result("", 0,$data,$count);
    }

    //续租类型图搜索
    public function histogram(Request $request)
    {
        $data = $this->LeaseRenewal->getHistogram($request, "lists");


        return result("", 0,$data);

    }

    //续租趋势图图搜索
    public function broken(Request $request)
    {
        $data = $this->LeaseRenewal->getbroken($request, "lists");


        return result("", 0,$data);

    }

    //续租区域图图搜索
    public function renewalArea(Request $request)
    {
        $data = $this->LeaseRenewal->getArea($request, "lists");


        return result("", 0,$data);

    }


    //续租类型图搜索
    public function advanceRenewal(Request $request)
    {
        $data = $this->LeaseRenewal->advanceRenewal($request, "lists");


        return result("", 0,$data);

    }

    //业务统计
        public function rankRentRenewal(Request $request)
    {

        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $date = date('Y-m-d',time());

        $provinces = allLeaseProvinces();
        $data = [];
        $rent_total_new = LeaseContractDateInfo::where('date',$date)->where('type',1)->where('province_id','<>',0)->get()->toArray();
        $rent_total_old = LeaseContractDateInfo::where('date',$date)->where('type',2)->where('province_id','<>',0)->get()->toArray();

        $newArray1 = array_column($rent_total_new,NULL,'province_id');
        $oldArray1 = array_column($rent_total_old,NULL,'province_id');



        $renewal_total = LeasePayment::selectRaw("count(id) as num,sum(rental) as total_rental,province_id")
            ->where("status","=",1)->whereIn("type",array('2','3'))
            ->groupBy("province_id")->get()->toArray();
        $renewalArray = array_column($renewal_total,NULL,'province_id');
//        $insurance_total = BlUserInsurance::selectRaw("count(id) as num,province_id")
//            ->where("status","=",20)
//            ->groupBy("province_id")->pluck("num", "province_id")->toArray();

        $retries = BlLeaseContract::selectRaw("sum(single_num) as retrie_num,province_id")
            ->where("status","=",3)
            ->groupBy("province_id")->get()->toArray();



        $retrieArray = array_column($retries,NULL,'province_id');

        foreach ($provinces as $key=>$value){
            $data[$key]['name'] = $value;
            if (isset($newArray1[$key])&& isset($oldArray1[$key])){
                $data[$key]['rent_total'] = $newArray1[$key]['total_num'] + $oldArray1[$key]['total_num'];
                $data[$key]['total_rental'] = $newArray1[$key]['total_rental'] + $oldArray1[$key]['total_rental'];
                $data[$key]['total_deposit'] = $newArray1[$key]['total_deposit'] + $oldArray1[$key]['total_deposit'];
            }else{
                $data[$key]['rent_total'] = 0;
                $data[$key]['total_rental'] = 0;
                $data[$key]['total_deposit'] = 0;
            }

            if (isset($retrieArray[$key])){
                $data[$key]['retrie_num'] = $retrieArray[$key]['retrie_num'];
            }else{
                $data[$key]['retrie_num'] = 0;
            }

            if (isset($renewalArray[$key])){
                $data[$key]['renewal_total'] = $renewalArray[$key]['num'];
                $data[$key]['renewal_money'] = $renewalArray[$key]['total_rental'];
            }else{
                $data[$key]['renewal_total'] = 0;
                $data[$key]['renewal_money'] = 0;
            }

//            if (isset($insurance_total[$key])){
//                $data[$key]['insurance_total'] = $insurance_total[$key];
//            }else{
//                $data[$key]['insurance_total'] = 0;
//            }
            $data[$key]['total_num'] = $data[$key]['rent_total'] + $data[$key]['renewal_total'];
            $data[$key]['rental'] = $data[$key]['total_rental'] + $data[$key]['renewal_money'];
//            if ($data[$key]['rent_total'] == 0){
//                $data[$key]['insurance_rate'] = "0%";
//            }else{
//                $data[$key]['insurance_rate'] =100*(sprintf("%.4f",$data[$key]['insurance_total']/$data[$key]['rent_total']))."%";
//            }

        }

        sort($data);
        array_multisort(array_column($data,'total_num'),SORT_DESC,$data);

        return response(['data'=>$data],200,['Access-Control-Allow-Origin'=>$origin]);
    }

    //业务趋势
    public function rentTrend(Request $request)
    {
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        //获取每个月份
//        $time = array();
//        $currentTime = time();
//        $cyear = floor(date("Y",$currentTime));
//        $cMonth = floor(date("m",$currentTime));
//        $Month  = [];
//        for($i=0;$i<12;$i++){
//            $Month[$i]= 1+$i.'月';
//            $nMonth = $cMonth-$i;
//            $cyear = $nMonth == 0 ? ($cyear-1) : $cyear;
//            $nMonth = $nMonth <= 0 ? 12+$nMonth : $nMonth;
//            if ($nMonth<10){
//                $nMonth = '0'.$nMonth;
//            }
//            $time[]['time']=$cyear.'-'.$nMonth;
//        }
        //2018 2019  2020
        $start_time1 = "2018-01-01";
        $end_time1 = "2018-12-31";

        $start_time2 = "2019-01-01";
        $end_time2 = "2019-12-31";

        $start_time3 = "2020-01-01";
        $end_time3 = "2020-12-31";
    //        $start_time = date("Y-m", strtotime("-11 months"));
    //        $end_time = date("Y-m", strtotime("+1 months"));

        $new_rent1 = LeaseContractDateInfo::selectRaw("sum(today_num) as num,date_format(date,'%m') as rent_month")->where('province_id',0)
            ->where('date','>=',$start_time1)->where('date',"<=",$end_time1)
            ->groupby('rent_month')->orderBy('rent_month')->pluck('num','rent_month');


        $renewal_rent1 = LeasePayment::selectRaw("count(id) as num,date_format(payed_at,'%m') as renewal_month")
            ->where("status","=",1)->whereIn("type",array('2','3'))
            ->where('payed_at','>=',$start_time1)->where('payed_at',"<=",$end_time1)
            ->groupBy("renewal_month")->orderBy('renewal_month')->pluck('num','renewal_month');


        $new_rent2 = LeaseContractDateInfo::selectRaw("sum(today_num) as num,date_format(date,'%m') as rent_month")->where('province_id',0)
            ->where('date','>=',$start_time2)->where('date',"<=",$end_time2)
            ->groupby('rent_month')->orderBy('rent_month')->pluck('num','rent_month');

        $renewal_rent2 = LeasePayment::selectRaw("count(id) as num,date_format(payed_at,'%m') as renewal_month")
            ->where("status","=",1)->whereIn("type",array('2','3'))
            ->where('payed_at','>=',$start_time2)->where('payed_at',"<=",$end_time2)
            ->groupBy("renewal_month")->orderBy('renewal_month')->pluck('num','renewal_month');

        $new_rent3 = LeaseContractDateInfo::selectRaw("sum(today_num) as num,date_format(date,'%m') as rent_month")->where('province_id',0)
            ->where('date','>=',$start_time3)->where('date',"<=",$end_time3)
            ->groupby('rent_month')->orderBy('rent_month')->pluck('num','rent_month');

        $renewal_rent3 = LeasePayment::selectRaw("count(id) as num,date_format(payed_at,'%m') as renewal_month")
            ->where("status","=",1)->whereIn("type",array('2','3'))
            ->where('payed_at','>=',$start_time3)->where('payed_at',"<=",$end_time3)
            ->groupBy("renewal_month")->orderBy('renewal_month')->pluck('num','renewal_month');



        $categories = [];
        for ($x=1; $x<=12; $x++) {
            if ($x<10) {
                $categories['0'.$x] = $x."月";
            }else{
                $categories[$x] = $x."月";
            }

        }

        $dataNew1 =  $dataNew2 =  $dataNew3 = $data1 = $data2 = $data3 = $data11 = $data21 = $data31 =[];
        foreach ($categories as $key=>$value){
            if (isset($new_rent1[$key])){
                $data1[$key] = $new_rent1[$key];
            }else{
                $data1[$key] = 0;
            }

            if (isset($renewal_rent1[$key])){
                $data11[$key] = $renewal_rent1[$key];
            }else{
                $data11[$key] = 0;
            }

            if (isset($new_rent2[$key])){
                $data2[$key] = $new_rent2[$key];
            }else{
                $data2[$key] = 0;
            }

            if (isset($renewal_rent2[$key])){
                $data21[$key] = $renewal_rent2[$key];
            }else{
                $data21[$key] = 0;
            }

            if (isset($new_rent3[$key])){
                $data3[$key] = $new_rent3[$key];
            }else{
                $data3[$key] = 0;
            }

            if (isset($renewal_rent3[$key])){
                $data31[$key] = $renewal_rent3[$key];
            }else{
                $data31[$key] = 0;
            }
            $date[] = $value;
            $dataNew1[] = $data1[$key] + $data11[$key];
            $dataNew2[] = $data2[$key] + $data21[$key];
            $dataNew3[] = $data3[$key] + $data31[$key];

        }


        $data = $month = $new = $renewal = array();
//        foreach ($time as $k =>$v){
//
//            if (isset($new_rent1[$v['time']])){
//                $new[$k] = $new_rent1[$v['time']];
//            }else{
//                $new[$k] = 0;
//            }
//
//            if (isset($renewal_rent1[$v['time']])){
//                $renewal[$k] = $renewal_rent1[$v['time']];
//            }else{
//                $renewal[$k] = 0;
//            }
//
//            $month[$k] = $v['time'];
//        }

        $data['categories']  = $date;
        $data['series'][0] = ['name'=>'2018','data'=>$dataNew1];
        $data['series'][1] = ['name'=>'2019','data'=>$dataNew2];
        $data['series'][2] = ['name'=>'2020','data'=>$dataNew3];

        return response(['data'=>$data],200,['Access-Control-Allow-Origin'=>$origin]);
    }

    //业务分析2
    public function rentAnalysisTypeNew(Request $request)
    {

        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $model_type = LeaseContract::selectRaw("count(id) as num,model_name")
            ->groupby('model_name')->orderBy('num','desc')->pluck('num','model_name')->toArray();
        $one = array_slice($model_type,0,3);
        asort($one);
        $other = array_sum($model_type)-array_sum($one);
        $one = array_merge(['其它'=>$other],$one);

        $data1 = [];
        $data['categories'] = array_keys($one);
        $data['series'][0] = ['data'=>array_values($one)];
        $total = array_sum($data['series'][0]['data']);
        foreach($data['categories'] as $k =>$v){

            $data1[$k]['name'] = $v."\r\n".(100*(sprintf("%.4f",$data['series'][0]['data'][$k]/$total)))."%";
            $data1[$k]['value'] = $data['series'][0]['data'][$k];

        }


        return response(['data'=>$data1],200,['Access-Control-Allow-Origin'=>$origin]);
    }

    //业务分析
    public function rentAnalysisType(Request $request)
    {

        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $model_type = LeaseContract::selectRaw("count(id) as num,model_name")
            ->groupby('model_name')->orderBy('num','desc')->pluck('num','model_name')->toArray();
        $one = array_slice($model_type,0,5);
        asort($one);
        $other = array_sum($model_type)-array_sum($one);
        $one = array_merge(['其它'=>$other],$one);

        $data['categories'] = array_keys($one);
        $data['series'][0] = ['data'=>array_values($one)];

        return response(['data'=>$data],200,['Access-Control-Allow-Origin'=>$origin]);
    }

    //业务分析周期
    public function rentCycle(Request $request)
    {
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $cycle_num = LeaseContract::selectRaw("count(id) as num,contract_term")->where('contract_term','!=','18')
         ->whereIn("status",[3,2,4,5,7,8])->groupby('contract_term')->pluck('num','contract_term')->toArray();

        $total = array_sum($cycle_num);
        $data = [];
        $a = 0;
        foreach ($cycle_num as $k=>$v){
            if ($k == 3){
                $data[$k]['name'] = "3个月".-(100*(sprintf("%.4f",$v/$total)))."%";
                $data[$k]['value'] = $v;
            }

            if ($k == 6){
                $data[$k]['name'] = "6个月".-(100*(sprintf("%.4f",$v/$total)))."%";
                $data[$k]['value'] = $v;
            }

            if ($k == 12){
                $data[$k]['name'] = "1年"."\r\n".(100*(sprintf("%.4f",$v/$total)))."%";
                $data[$k]['value'] = $v;
            }else{
                $a +=$v;
            }

            if ($k == 15){
                $data[$k]['name'] = "15个月".-(100*(sprintf("%.4f",$v/$total)))."%";
                $data[$k]['value'] = $v;
            }

            if ($k == 24){
                $data[$k]['name'] = "2年".-(100*(sprintf("%.4f",$v/$total)))."%";
                $data[$k]['value'] = $v;
            }
        }

        $data1 = [];
        $data1[12]['name'] = $data[12]['name'];
        $data1[12]['value'] = $data[12]['value'];

        $data1[1]['name'] = "其他"."\r\n".(100*(sprintf("%.4f",$a/$total)))."%";
        $data1[1]['value'] = $a;
        sort($data1);

        return response(['data'=>$data1],200,['Access-Control-Allow-Origin'=>$origin]);
    }

    //业务汇总
    public function rentSummary(Request $request)
    {
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';

        $date = date('Y-m-d',time());
        $data = [];
        //租赁总量
        $rent_total_new = LeaseContractDateInfo::where('date',$date)->where('type',1)->where('province_id',0)->first();
        $rent_total_old = LeaseContractDateInfo::where('date',$date)->where('type',2)->where('province_id',0)->first();
        $renewal_total = LeasePayment::selectRaw("count(id) as num")
            ->where("status","=",1)->whereIn("type",array('2','3'))
            ->first();


        $total_old = $total_new = $renewal_num = 0;
        if ($rent_total_new) $total_new = $rent_total_new->total_num;
        if ($rent_total_old) $total_old = $rent_total_old->total_num;
        if ($renewal_total) $renewal_num = $renewal_total->num;
        $rent_total = $total_new +  $total_old + $renewal_num;

        //用户量
        $user_num = LeaseUser::count();

        //网点量
        $service_num = LeaseService::whereIn('status',[1,0])->count();

        //网点收益
        $income = LeaseIncomeLog::sum('total');


        $data[0]['suffixText'] = '租赁总量';
        $data[0]['data'] = $rent_total;

        $data[1]['suffixText'] ='用户量';
        $data[1]['data'] =  $user_num;

        $data[2]['suffixText'] = '网点量' ;
        $data[2]['data'] = $service_num;

        $data[3]['suffixText'] ='网点收益';
        $data[3]['data'] =  $income;

        return response(['data'=>$data],200,['Access-Control-Allow-Origin'=>$origin]);
    }


    //业务地图
    public function rentMap(Request $request)
    {
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $provinces = allLeaseProvinces();
        $date = date('Y-m-d',time());

        $data[0]['name'] = "湖南";
        $data[0]['value'] = ['name'=>'湖南','rent_num'=>0,'user_num'=>0];
        $data[0]['lng'] = 112.982279;
        $data[0]['lat'] = 28.19409;
        $data[0]['zoom'] = 1;

        $data[1]['name'] = "安徽";
        $data[1]['value'] = ['name'=>'安徽','rent_num'=>0,'user_num'=>0];
        $data[1]['lng'] = 117.283042;
        $data[1]['lat'] = 31.86119;
        $data[1]['zoom'] = 1;

        $data[2]['name'] = "江西";
        $data[2]['value'] = ['name'=>'江西','rent_num'=>0,'user_num'=>0];
        $data[2]['lng'] = 115.892151;
        $data[2]['lat'] = 28.676493;
        $data[2]['zoom'] = 1;

        $data[3]['name'] = "河南";
        $data[3]['value'] = ['name'=>'河南','rent_num'=>0,'user_num'=>0];
        $data[3]['lng'] = 113.665412;
        $data[3]['lat'] = 34.757975;
        $data[3]['zoom'] = 1;

        $data[4]['name'] = "广西";
        $data[4]['value'] = ['name'=>'广西','rent_num'=>0,'user_num'=>0];
        $data[4]['lng'] = 108.320004;
        $data[4]['lat'] = 22.82402;
        $data[4]['zoom'] = 1;

        $data[5]['name'] = "江苏";
        $data[5]['value'] = ['name'=>'江苏','rent_num'=>0,'user_num'=>0];
        $data[5]['lng'] = 118.767413;
        $data[5]['lat'] = 32.041544;
        $data[5]['zoom'] = 1;

        $data[6]['name'] = "湖北";
        $data[6]['value'] = ['name'=>'湖北','rent_num'=>0,'user_num'=>0];
        $data[6]['lng'] = 114.298572;
        $data[6]['lat'] = 30.584355;
        $data[6]['zoom'] = 1;

        $data[7]['name'] = "福建";
        $data[7]['value'] = ['name'=>'福建','rent_num'=>0,'user_num'=>0];
        $data[7]['lng'] = 119.306239;
        $data[7]['lat'] = 26.075302;
        $data[7]['zoom'] = 1;

        $data[8]['name'] = "浙江";
        $data[8]['value'] = ['name'=>'浙江','rent_num'=>0,'user_num'=>0];
        $data[8]['lng'] = 120.153576;
        $data[8]['lat'] = 30.287459;
        $data[8]['zoom'] = 1;

        $data[9]['name'] = "台湾";
        $data[9]['value'] = ['name'=>'台湾','rent_num'=>0,'user_num'=>0];
        $data[9]['lng'] = 121.509062;
        $data[9]['lat'] = 25.044332;
        $data[9]['zoom'] = 1;

        $data[10]['name'] = "山东";
        $data[10]['value'] = ['name'=>'山东','rent_num'=>0,'user_num'=>0];
        $data[10]['lng'] = 117.000923;
        $data[10]['lat'] = 36.675807;
        $data[10]['zoom'] = 1;

        $data[11]['name'] = "上海市";
        $data[11]['value'] = ['name'=>'上海市','rent_num'=>0,'user_num'=>0];
        $data[11]['lng'] = 121.472644;
        $data[11]['lat'] = 31.231706;
        $data[11]['zoom'] = 1;

        $data[12]['name'] = "海南";
        $data[12]['value'] =  ['name'=>'海南','rent_num'=>0,'user_num'=>0];
        $data[12]['lng'] = 110.33119;
        $data[12]['lat'] = 20.031971;
        $data[12]['zoom'] = 1;


        foreach($data as $key=>$value){
            foreach ($provinces as $k=>$v){
                if ($value['name'] == $v){
                    $data[$key]['province_id'] = $k;

                }

            }
        }

        //用户量
        $user = LeaseUser::selectRaw("count(id) as num,province_id")
            ->groupBy('province_id')->pluck("num", "province_id")->toArray();

        //租赁总量
        $rent_total_new = LeaseContractDateInfo::where('date',$date)->where('type',1)->where('province_id','<>',0)->get()->toArray();
        $rent_total_old = LeaseContractDateInfo::where('date',$date)->where('type',2)->where('province_id','<>',0)->get()->toArray();

        $newArray1 = array_column($rent_total_new,NULL,'province_id');
        $oldArray1 = array_column($rent_total_old,NULL,'province_id');

        $renewal_total = LeasePayment::selectRaw("count(id) as num,province_id")
            ->where("status","=",1)->whereIn("type",array('2','3'))
            ->groupBy("province_id")->pluck("num", "province_id")->toArray();

        $datas = [];
        foreach ($provinces as $key=>$value){

            if (isset($newArray1[$key])&& isset($oldArray1[$key])){
                $datas[$key]['total_num'] = $newArray1[$key]['total_num'] + $oldArray1[$key]['total_num'];
            }else{
                $datas[$key]['total_num'] = 0;
            }
            if (isset($renewal_total[$key])){
                $datas[$key]['renewal_total'] = $renewal_total[$key];
            }else{
                $datas[$key]['renewal_total'] = 0;
            }

            $datas[$key]['rent_total'] = $datas[$key]['total_num'] + $datas[$key]['renewal_total'];
        }


        foreach ($data as $k1=>$v1){
            if (isset($datas[$v1['province_id']])){
                $data[$k1]['value']['rent_num'] = $datas[$v1['province_id']]['total_num'];
            }

            if (isset($user[$v1['province_id']])){
                $data[$k1]['value']['user_num'] = $user[$v1['province_id']];
            }
        }

//
//        (name, data) => {
//        return '<div style="font-size:10px;line-height:30px;padding:5px 20px;">'+
//            '<p>'+name.value[2].name+'</p>'+
//            '<p>租赁数：'+name.value[2].rent_num+'</p>'+
//            '<p>用户数：'+name.value[2].user_num+'</p>'+
//            '</div>';
//    }

        return response(['data'=>$data],200,['Access-Control-Allow-Origin'=>$origin]);
    }



    //业务汇总
    public function rentSummaryNew($id,Request $request)
    {
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';

        $date = date('Y-m-d',time());
        $data = [];
        //租赁总量
        $rent_total_new = LeaseContractDateInfo::where('date',$date)->where('type',1)->where('province_id',0)->first();
        $rent_total_old = LeaseContractDateInfo::where('date',$date)->where('type',2)->where('province_id',0)->first();
        $renewal_total = LeasePayment::selectRaw("count(id) as num")
            ->where("status","=",1)->whereIn("type",array('2','3'))
            ->first();


        $depoit_new = $depoit_old = $rental_old = $rental_new = $total_old = $total_new = $renewal_num = 0;
        if ($rent_total_new){
            $total_new = $rent_total_new->total_num;
            $rental_new = $rent_total_new->total_rental;
            $depoit_new = $rent_total_new->total_deposit;
        }
        if ($rent_total_old){
            $total_old = $rent_total_old->total_num;
            $rental_old = $rent_total_old->total_rental;
            $depoit_old = $rent_total_old->total_deposit;
        }
        if ($renewal_total){
            $renewal_num = $renewal_total->num;
        }
        //租赁量
        $rent_total = $total_new +  $total_old + $renewal_num;

        //租赁金额
        $rent_money = $rental_new +  $rental_old;

        //租赁押金
        $depoit = $depoit_new +  $depoit_old;

        //回收电池
        $retries = BlLeaseContract::where("status","=",3)->sum('single_num');

        //用户量
        $user_num = LeaseUser::count();

        //网点量
        $service_num = LeaseService::whereIn('status',[1,0])->count();

        //网点收益
//        $income = LeaseIncomeLog::sum('total');


//        $retrieArray = array_column($retries,NULL,'province_id');
        if($id == 1){
            $data[0]['prefixText'] = '用户数（人）';
            $data[0]['data'] = $user_num;

        }

        if($id == 2){
            $data[0]['prefixText'] = '网点数（家）';
            $data[0]['data'] = $service_num;
        }

        if($id == 3){
            $data['label'] = '租赁量（组）';
            $data['data'] = 100;
            $data['value'] = $rent_total;
        }

        if($id == 4){
            $data['label'] = '租赁金额（元）';
            $data['data'] = 100;
            $data['value'] = $rent_money;
        }


        if($id == 5){
            $data['label'] = '租赁押金（元）';
            $data['data'] = 100;
            $data['value'] = $depoit;


        }

        if($id == 6){
            $data['label'] = '回收电池（只）';
            $data['data'] = 100;
            $data['value'] = $retries;
        }




//        $data[1]['suffixText'] ='用户量';
//        $data[1]['data'] =  $user_num;
//
//        $data[2]['suffixText'] = '网点量' ;
//        $data[2]['data'] = $service_num;
//
//        $data[3]['suffixText'] = '网点量' ;
//        $data[3]['data'] = $service_num;
//        $data[3]['suffixText'] ='网点收益';
//        $data[3]['data'] =  $income;

        return response(['data'=>$data],200,['Access-Control-Allow-Origin'=>$origin]);
    }

//快点电池销售趋势
    public function saleOrder(Request $request)
    {
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';

        //2017 2018 2019  2020
        $start_time1 = "1483200000";
        $end_time1 = "1514649600";

        $start_time2 = "1514736000";
        $end_time2 = "1546185600";

        $start_time3 = "1546272000";
        $end_time3 = "1577721600";

        $start_time4 = "1577808000";
        $end_time4 = "1609344000";


        $new_rent1 = SaleOrder::selectRaw("(sum(total_amount)/10000) as num,FROM_UNIXTIME(confirmed_at,'%m') as rent_month")
            ->where('confirmed_at','>=',$start_time1)->where('confirmed_at',"<=",$end_time1)
            ->whereIn('status',[221,232,242,252,281])
            ->groupby('rent_month')->orderBy('rent_month')->pluck('num','rent_month');


        $new_rent2 = SaleOrder::selectRaw("(sum(total_amount)/10000) as num,FROM_UNIXTIME(confirmed_at,'%m') as rent_month")
            ->where('confirmed_at','>=',$start_time2)->where('confirmed_at',"<=",$end_time2)
            ->whereIn('status',[221,232,242,252,281])
            ->groupby('rent_month')->orderBy('rent_month')->pluck('num','rent_month');

        $new_rent3 = SaleOrder::selectRaw("(sum(total_amount)/10000) as num,FROM_UNIXTIME(confirmed_at,'%m') as rent_month")
            ->where('confirmed_at','>=',$start_time3)->where('confirmed_at',"<=",$end_time3)
            ->whereIn('status',[221,232,242,252,281])
            ->groupby('rent_month')->orderBy('rent_month')->pluck('num','rent_month');

        $new_rent4 = SaleOrder::selectRaw("(sum(total_amount)/10000) as num,FROM_UNIXTIME(confirmed_at,'%m') as rent_month")
            ->where('confirmed_at','>=',$start_time4)->where('confirmed_at',"<=",$end_time4)
            ->whereIn('status',[221,232,242,252,281])
            ->groupby('rent_month')->orderBy('rent_month')->pluck('num','rent_month');



        $categories = [];
        for ($x=1; $x<=12; $x++) {
            if ($x<10) {
                $categories['0'.$x] = $x."月";
            }else{
                $categories[$x] = $x."月";
            }

        }

        $dataNew1 =  $dataNew2 =  $dataNew3 = $data1 = $data2 = $data3 = $data11 = $data21 = $data31 =[];
        foreach ($categories as $key=>$value){
            if (isset($new_rent1[$key])){
                $data1[] = $new_rent1[$key];
            }else{
                $data1[] = 0;
            }



            if (isset($new_rent2[$key])){
                $data2[] = $new_rent2[$key];
            }else{
                $data2[] = 0;
            }



            if (isset($new_rent3[$key])){
                $data3[] = $new_rent3[$key];
            }else{
                $data3[] = 0;
            }


            if (isset($new_rent4[$key])){
                $data4[] = $new_rent4[$key];
            }else{
                $data4[] = 0;
            }

            $date[] = $value;

        }


        $data = $month = $new = $renewal = array();

        $data['categories']  = $date;
        $data['series'][0] = ['name'=>'2017','data'=>$data1];
        $data['series'][1] = ['name'=>'2018','data'=>$data2];
        $data['series'][2] = ['name'=>'2019','data'=>$data3];
        $data['series'][3] = ['name'=>'2020','data'=>$data4];

        return response(['data'=>$data],200,['Access-Control-Allow-Origin'=>$origin]);
    }

    //快点电池回收趋势
    public function retrieOrder(Request $request)
    {
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';

        //2017 2018 2019  2020
        $start_time1 = "1483200000";
        $end_time1 = "1514649600";

        $start_time2 = "1514736000";
        $end_time2 = "1546185600";

        $start_time3 = "1546272000";
        $end_time3 = "1577721600";

        $start_time4 = "1577808000";
        $end_time4 = "1609344000";


        $new_rent1 = RecycleOrder::selectRaw("(sum(weight)/1000) as num,FROM_UNIXTIME(confirmed_at,'%m') as rent_month")
            ->where('confirmed_at','>=',$start_time1)->where('confirmed_at',"<=",$end_time1)
            ->where('status',288)
            ->groupby('rent_month')->orderBy('rent_month')->pluck('num','rent_month');


        $new_rent2 = RecycleOrder::selectRaw("(sum(weight)/1000) as num,FROM_UNIXTIME(confirmed_at,'%m') as rent_month")
            ->where('confirmed_at','>=',$start_time2)->where('confirmed_at',"<=",$end_time2)
            ->where('status',288)
            ->groupby('rent_month')->orderBy('rent_month')->pluck('num','rent_month');

        $new_rent3 = RecycleOrder::selectRaw("(sum(weight)/1000) as num,FROM_UNIXTIME(confirmed_at,'%m') as rent_month")
            ->where('confirmed_at','>=',$start_time3)->where('confirmed_at',"<=",$end_time3)
            ->where('status',288)
            ->groupby('rent_month')->orderBy('rent_month')->pluck('num','rent_month');

        $new_rent4 = RecycleOrder::selectRaw("(sum(weight)/1000) as num,FROM_UNIXTIME(confirmed_at,'%m') as rent_month")
            ->where('confirmed_at','>=',$start_time4)->where('confirmed_at',"<=",$end_time4)
            ->where('status',288)
            ->groupby('rent_month')->orderBy('rent_month')->pluck('num','rent_month');



        $categories = [];
        for ($x=1; $x<=12; $x++) {
            if ($x<10) {
                $categories['0'.$x] = $x."月";
            }else{
                $categories[$x] = $x."月";
            }

        }

        $dataNew1 =  $dataNew2 =  $dataNew3 = $data1 = $data2 = $data3 = $data11 = $data21 = $data31 =[];
        foreach ($categories as $key=>$value){
            if (isset($new_rent1[$key])){
                $data1[] = $new_rent1[$key];
            }else{
                $data1[] = 0;
            }



            if (isset($new_rent2[$key])){
                $data2[] = $new_rent2[$key];
            }else{
                $data2[] = 0;
            }



            if (isset($new_rent3[$key])){
                $data3[] = $new_rent3[$key];
            }else{
                $data3[] = 0;
            }


            if (isset($new_rent4[$key])){
                $data4[] = $new_rent4[$key];
            }else{
                $data4[] = 0;
            }

            $date[] = $value;

        }


        $data = $month = $new = $renewal = array();

        $data['categories']  = $date    ;
        $data['series'][0] = ['name'=>'2017','data'=>$data1];
        $data['series'][1] = ['name'=>'2018','data'=>$data2];
        $data['series'][2] = ['name'=>'2019','data'=>$data3];
        $data['series'][3] = ['name'=>'2020','data'=>$data4];

        return response(['data'=>$data],200,['Access-Control-Allow-Origin'=>$origin]);
    }


    public function koodTable(Request $request)
    {

        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $date = date('Y-m-d',time());

        $provinces = allLeaseProvinces();

        $provinces = Sites::pluck("name","id");


        $data = [];
//        $rent_total_new = LeaseContractDateInfo::where('date',$date)->where('type',1)->where('province_id','<>',0)->get()->toArray();
//        $rent_total_old = LeaseContractDateInfo::where('date',$date)->where('type',2)->where('province_id','<>',0)->get()->toArray();
//
//        $newArray1 = array_column($rent_total_new,NULL,'province_id');
//        $oldArray1 = array_column($rent_total_old,NULL,'province_id');


        $renewal_total = SaleOrder::selectRaw("convert(sum(total_amount)/10000,decimal(10,2)) as money,count(id) as order_num,site_id,sum(num) as bar_num")
            ->whereIn('status',[221,232,242,252,281])
            ->groupBy("site_id")->get()->toArray();



        $renewalArray = array_column($renewal_total,NULL,'site_id');



        foreach ($provinces as $key=>$value){
            $data[$key]['name'] = $value;


            if (isset($renewalArray[$key])){
                $data[$key]['money'] = $renewalArray[$key]['money'];
                $data[$key]['order_num'] = $renewalArray[$key]['order_num'];
                $data[$key]['bar_num'] = $renewalArray[$key]['bar_num'];
            }else{
                $data[$key]['money'] = 0;
                $data[$key]['order_num'] = 0;
                $data[$key]['bar_num'] = 0;
            }


        }

        sort($data);

        array_multisort(array_column($data,'money'),SORT_DESC,$data);

        return response(['data'=>$data],200,['Access-Control-Allow-Origin'=>$origin]);
    }

    //业务汇总
    public function koodSummaryNew(Request $request)
    {
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $data = [];

        //电池销售额(万元)
        $money_total =  SaleOrder::selectRaw("convert(sum(total_amount)/10000,decimal(10,2)) as money")
            ->whereIn('status',[221,232,242,252,281])
            ->first();

        //电池回收量(吨)
        $bar_weight = RecycleOrder::selectRaw("convert(sum(weight)/1000,decimal(10,2)) as num")
            ->where('status',288)
            ->first();

        //商户数(家)
        $users = User::where('authentic_status',2)
            ->count("id");

        //仓库数(个)
        $depot = depot::where('status',1)
            ->count("id");

        $money = $weight = $user_num = $depot_num = 0;
        if ($money_total){
            $money = $money_total->money;
        }

        if ($bar_weight){
            $weight = $bar_weight->num;
        }

        if ($users){
            $user_num = $users;
        }

        if ($depot){
            $depot_num = $depot;
        }

        $data[0]['prefixText'] = '电池销售额(万元)';
        $data[0]['data'] = $money;

        $data[1]['prefixText'] ='电池回收量(吨)';
        $data[1]['data'] =  $weight;

        $data[2]['prefixText'] = '商家数(家)' ;
        $data[2]['data'] = $user_num;

        $data[3]['prefixText'] = '仓库数(个)' ;
        $data[3]['data'] = $depot_num;
//        $data[3]['suffixText'] ='网点收益';
//        $data[3]['data'] =  $income;

        return response(['data'=>$data],200,['Access-Control-Allow-Origin'=>$origin]);
    }





}
