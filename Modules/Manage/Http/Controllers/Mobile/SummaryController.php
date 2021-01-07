<?php

namespace Modules\Manage\Http\Controllers\Mobile;

use App\Models\Manager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Lease\Models\BlLeaseContract;
use Modules\Lease\Models\BlLeaseExchange;
use Modules\Lease\Models\BlLeaseLost;
use Modules\Lease\Models\BlLeaseRetire;
use Modules\Lease\Models\BlUserInsurance;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Service\LeaseIncomeLog;
use Modules\Manage\Models\Service\LeaseServiceWithdrawLog;
use Modules\Manage\Models\Service\ServiceStock;
use Modules\Manage\Repositories\ManageMenuRepository;

class SummaryController extends Controller
{

    public function __construct()
    {

    }

    public function managerMenu(Request $request)
    {




        $all = allLeaseProvinces();
        $all[0] = "全部区域";

        $agent_id = $request->manager->agent_id;


        if (!$agent_id) {
            return result('', 1, $all);

        } else {
            $data = [];
            $data[$agent_id] = $all[$agent_id];
            return result('', 1, $data);
        }


    }


    public function businessData(Request $request)
    {
        $time = time();
        $date_time = date('Y-m-d H:i:s', $time);

        //时间选择
        $timetype = $this->timeBegin($request->type);
        // 省份选择
        $agent_id = $request->manager->agent_id;


        //  租赁总数
        //新租
        $contract_total = BlLeaseContract::whereIn('status', [3, 4, 5, 7, 8])->where('lease_type', 0)->where('term_index', '<=', 1);

        //续约
        $renewal_totaol1 = BlLeaseContract::whereIn('status', [3, 4, 5, 7, 8])->where('lease_type', 1);

        //续租
        $renewal_totaol2 = BlLeaseContract::whereIn('status', [3, 4, 5, 7, 8])->where('term_index', '>', 1)->where('lease_type', 0);


        //换租数
        $change_total = BlLeaseExchange::select("confirmed_at", "contract_expired_at", "province_id", "reason")
            ->leftJoin('bl_lease_services', 'bl_lease_exchanges.id', '=', 'bl_lease_services.serviceable_id')
            ->leftJoin('bl_lease_contracts', 'bl_lease_contracts.id', '=', 'bl_lease_services.contract_id')
            ->where('bl_lease_services.serviceable_type', '=', 'App\Models\LeaseExchangeModel')->where('bl_lease_exchanges.status', '=', 4);

        //退租
        $retire = BlLeaseRetire::select("confirmed_at", "contract_expired_at", "province_id", 'amount')
            ->leftJoin('bl_lease_services', 'bl_lease_retires.id', '=', 'bl_lease_services.serviceable_id')
            ->leftJoin('bl_lease_contracts', 'bl_lease_contracts.id', '=', 'bl_lease_services.contract_id')
            ->where('bl_lease_services.serviceable_type', '=', 'App\Models\LeaseRetireModel')
            ->where('bl_lease_retires.status', '=', 3);

        // 投保
        $insurance = BlUserInsurance::select('province_id', 'contract_no');


        //报失
        $report_lost_num = BlLeaseLost::selectRaw("count(id) as lost_num,province_id");


        if ($timetype) {
            $contract_total = $contract_total->whereBetween("effected_at", [$timetype, $date_time]);
            $renewal_totaol1 = $renewal_totaol1->whereBetween("effected_at", [$timetype, $date_time]);
            $renewal_totaol2 = $renewal_totaol2->whereBetween("effected_at", [$timetype, $date_time]);

            $change_total = $change_total->whereBetween("effected_at", [$timetype, $date_time]);
            $retire = $retire->whereBetween("effected_at", [$timetype, $date_time]);

            $insurance = $insurance->whereBetween("created_at", [$timetype, $date_time]);
            $report_lost_num = $report_lost_num->whereBetween("created_at", [$timetype, $date_time]);
        }

        if ($agent_id) {
            $contract_total = $contract_total->where("province_id", $agent_id);
            $renewal_totaol1 = $renewal_totaol1->where("province_id", $agent_id);
            $renewal_totaol2 = $renewal_totaol2->where("province_id", $agent_id);

            $change_total = $change_total->where("bl_lease_contracts.province_id", $agent_id);
            $retire = $retire->where("bl_lease_contracts.province_id", $agent_id);

            $insurance = $insurance->where("province_id", $agent_id);
            $report_lost_num = $report_lost_num->where("province_id", $agent_id);
        }

        $contract_total = $contract_total->count();
        $renewal_totaol1 = $renewal_totaol1->count();
        $renewal_totaol2 = $renewal_totaol2->count();

        // 租赁总数
        $rent_total = $contract_total + $renewal_totaol1 + $renewal_totaol2;
        // 续租总数
        $renewal_total = $renewal_totaol1 + $renewal_totaol2;
        // 换组总数
        $change_total = $change_total->count();
        //退租总数
        $retire = $retire->count();
        //投保总数
        $insurance = $insurance->count();
        //报失总数
        $report_lost_num = $report_lost_num->count();

        $data[0]['name'] = "租赁数";
        $data[0]['value'] = $rent_total;
        $data[1]['name'] = "续租数";
        $data[1]['value'] = $renewal_total;
        $data[2]['name'] = "换租数";
        $data[2]['value'] = $change_total;
        $data[3]['name'] = "退租数";
        $data[3]['value'] = $retire;
        $data[4]['name'] = "投保数";
        $data[4]['value'] = $insurance;
        $data[5]['name'] = "电池报失";
        $data[5]['value'] = $report_lost_num;


        return result('', 1, $data);
    }


    public function financialData(Request $request)
    {
        $time = time();
        $date_time = date('Y-m-d H:i:s', $time);

        //时间选择
        $timetype = $this->timeBegin($request->type);
        // 省份选择
        $agent_id = $request->manager->agent_id;


        //租赁金额
        $contract_total = LeaseContract::whereIn('status', [3, 4, 5, 7, 8])->where('lease_type', 0)->where('term_index', '<=', 1);

        //续约
        $renewal_totaol1 = LeaseContract::whereIn('status', [3, 4, 5, 7, 8])->where('lease_type', 1);

        //续租
        $renewal_totaol2 = LeaseContract::whereIn('status', [3, 4, 5, 7, 8])->where('term_index', '>', 1)->where('lease_type', 0);

        //退租
        $retire = BlLeaseRetire::select("confirmed_at", "contract_expired_at", "province_id", 'amount')
            ->leftJoin('bl_lease_services', 'bl_lease_retires.id', '=', 'bl_lease_services.serviceable_id')
            ->leftJoin('bl_lease_contracts', 'bl_lease_contracts.id', '=', 'bl_lease_services.contract_id')
            ->where('bl_lease_services.serviceable_type', '=', 'App\Models\LeaseRetireModel')
            ->where('bl_lease_retires.status', '=', 3);

        //网点收益
        $income = LeaseIncomeLog::select("json", "total", "date");

        //网点提现
        $withdraw = LeaseServiceWithdrawLog::select("json_amount", "total", "date");

        if ($timetype) {
            $contract_total = $contract_total->whereBetween("effected_at", [$timetype, $date_time]);
            $renewal_totaol1 = $renewal_totaol1->whereBetween("effected_at", [$timetype, $date_time]);
            $renewal_totaol2 = $renewal_totaol2->whereBetween("effected_at", [$timetype, $date_time]);
            $retire = $retire->whereBetween("effected_at", [$timetype, $date_time]);
            $income = $income->whereBetween("date", [$timetype, $date_time]);
            $withdraw = $withdraw->whereBetween("date", [$timetype, $date_time]);


        }

        $income_amount = $withdraw_amount = 0;
        $income_array = $income->get()->toArray();
        $withdraw_array = $withdraw->get()->toArray();
        if ($agent_id) {
            $contract_total = $contract_total->where("province_id", $agent_id);
            $renewal_totaol1 = $renewal_totaol1->where("province_id", $agent_id);
            $renewal_totaol2 = $renewal_totaol2->where("province_id", $agent_id);
            $retire = $retire->where("bl_lease_contracts.province_id", $agent_id);


            foreach ($income_array as $key => $value) {
                $json = json_decode($value['json'], true);
                if (isset($json[$agent_id])) $income_amount += $json[$agent_id];


            }

            foreach ($withdraw_array as $k => $v) {
                $json_amount = json_decode($v['json_amount'], true);
                if (isset($json_amount[$agent_id])) $withdraw_amount += $json_amount[$agent_id];
            }
        } else {
            $income_amount = $income->sum('total');
            $withdraw_amount = $withdraw->sum('total');
        }


        $contract_total_1 = $contract_total->sum('rental_all');
        $renewal_totaol_1 = $renewal_totaol1->sum('rental_all');
        $renewal_totaol_2 = $renewal_totaol2->sum('rental_all');

        $contract_deposit = $contract_total->sum('deposit');
        $renewal_deposit1 = $renewal_totaol1->sum('deposit');
        $renewal_deposit2 = $renewal_totaol2->sum('deposit');


        // 租赁金额
        $rent_total = $contract_total_1 + $renewal_totaol_1 + $renewal_totaol_2;
        // $renewal_totaol_1
        $rent_deposit = $contract_deposit + $renewal_deposit1 + $renewal_deposit2;
        // 续租金额
        $renewal_total = $renewal_totaol_1 + $renewal_totaol_2;
        //退租金额
        $retire = $retire->sum('amount');


        $data[0]['name'] = "租赁金额";
        $data[0]['value'] = $rent_total;
        $data[1]['name'] = "续租金额";
        $data[1]['value'] = $renewal_total;
        $data[2]['name'] = "退租金额";
        $data[2]['value'] = $retire;
        $data[3]['name'] = "租赁押金";
        $data[3]['value'] = $rent_deposit;
        $data[4]['name'] = "网点收益";
        $data[4]['value'] = $income_amount;
        $data[5]['name'] = "网点提现";
        $data[5]['value'] = $withdraw_amount;


        return result('', 1, $data);
    }

    // 租赁成交量
    public function LeaseVolume(Request $request)
    {
        try {
            //当前登陆人可查看区域
            $provinceId = $request->manager->agent_id;
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //根据查看权限判断分组字段
            $groupByColumn = $provinceId ? 'city_id' : 'province_id';
            //判断获取全新备用电池还是废旧电池数据
            // 新租
            $sql = "count(id) as rent_new";
            $data1 = LeaseContract::query()
                ->selectRaw("$groupByColumn,$sql")
                ->when($begin, function ($query) use ($begin) {
                    $query->where('effected_at', '>', $begin);
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('province_id', $provinceId);
                })->whereIn('status', [3, 4, 5, 7, 8])->where('lease_type', 0)->where('term_index', '<=', 1)
                ->where($groupByColumn, '>', 0)
                ->groupBy($groupByColumn)->pluck("rent_new",$groupByColumn);



            //续租
            $data2 = LeaseContract::query()
                ->selectRaw("$groupByColumn,$sql")
                ->when($begin, function ($query) use ($begin) {
                    $query->where('effected_at', '>', $begin);
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('province_id', $provinceId);
                })->whereIn('status', [3, 4, 5, 7, 8])
                ->where('term_index', '>', 1)->where('lease_type', 0)
                ->groupBy($groupByColumn)->pluck("rent_new",$groupByColumn);

            //续约
            $data3 =  LeaseContract::query()
                ->selectRaw("$groupByColumn,$sql")
                ->when($begin, function ($query) use ($begin) {
                    $query->where('effected_at', '>', $begin);
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('province_id', $provinceId);
                })->whereIn('status', [3, 4, 5, 7, 8])->where('lease_type', 1)
                ->groupBy($groupByColumn)->pluck("rent_new",$groupByColumn);


            //解析地区名
            if ($provinceId){
                $agents = agentsTree();
                foreach ($agents as $k=>$v){
                    if ($v['id'] ==$provinceId )$province[] = $v;
                }

                foreach ($province[0]['child'] as $key=>$value){
                    $data[$key]['name'] = $value['name'];
                    if (isset($data1[$value['id']])){
                        $data[$key]['rent_new'] = $data1[$value['id']];
                    }else{
                        $data[$key]['rent_new'] = 0;
                    }

                    if (isset($data2[$value['id']])){
                        $data[$key]['renewal1'] = $data2[$value['id']];
                    }else{
                        $data[$key]['renewal1'] = 0;
                    }

                    if (isset($data3[$value['id']])){
                        $data[$key]['renewal2'] = $data3[$value['id']];
                    }else{
                        $data[$key]['renewal2'] = 0;
                    }

                    $data[$key]['renewal'] = $data[$key]['renewal2'] + $data[$key]['renewal1'];

                }
            }else{
                $province = allLeaseProvinces();

                foreach ($province as $key=>$value){
                    $data[$key]['name'] = $value;
                    if (isset($data1[$key])){
                        $data[$key]['rent_new'] = $data1[$key];
                    }else{
                        $data[$key]['rent_new'] = 0;
                    }

                    if (isset($data2[$key])){
                        $data[$key]['renewal1'] = $data2[$key];
                    }else{
                        $data[$key]['renewal1'] = 0;
                    }

                    if (isset($data3[$key])){
                        $data[$key]['renewal2'] = $data3[$key];
                    }else{
                        $data[$key]['renewal2'] = 0;
                    }

                    $data[$key]['renewal'] = $data[$key]['renewal2'] + $data[$key]['renewal1'];

                }
                sort($data);

            }

            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }

    }


    // 网点新增排行
    public function serviceRank(Request $request)
    {
        try {
            //当前登陆人可查看区域
            $provinceId = $request->manager->agent_id;
            //选择时间范围
            $begin = $this->timeSelect($request->get('type', 1));
            //根据查看权限判断分组字段
            $groupByColumn = $provinceId ? 'city_id' : 'province_id';

            //            // 新租
            $sql = "count(id) as added";
            $data1 = LeaseService::query()
                ->selectRaw("$groupByColumn,$sql")
                ->when($begin, function ($query) use ($begin) {
                    $query->where('audited_at', '>', $begin);
                })->when($provinceId, function ($query) use ($provinceId) {
                    $query->where('province_id', $provinceId);
                })->where('status', 1)->where($groupByColumn, '>', 0)
                ->groupBy($groupByColumn)->pluck("added",$groupByColumn);


            //解析地区名
            if ($provinceId){
                $agents = agentsTree();
                foreach ($agents as $k=>$v){
                    if ($v['id'] ==$provinceId )$province[] = $v;
                }

                foreach ($province[0]['child'] as $key=>$value){
                    $data[$key]['name'] = $value['name'];
                    if (isset($data1[$value['id']])){
                        $data[$key]['added'] = $data1[$value['id']];
                    }else{
                        $data[$key]['added'] = 0;
                    }
                }
                array_multisort(array_column($data,'added'),SORT_DESC,$data);
            }else{
                $province = allLeaseProvinces();

                foreach ($province as $key=>$value){
                    $data[$key]['name'] = $value;
                    if (isset($data1[$key])){
                        $data[$key]['added'] = $data1[$key];
                    }else{
                        $data[$key]['added'] = 0;
                    }

                }
                sort($data);
                array_multisort(array_column($data,'added'),SORT_DESC,$data);
            }

            return result('', 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }

    }
}
