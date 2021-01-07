<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/9/6 10:24
 */

namespace Modules\Manage\Http\Controllers\Crm;

use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Lease\Models\BlAppDown;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Crm\CrmContact;
use Modules\Manage\Models\Crm\CrmPlanRecord;
use Modules\Manage\Models\Crm\CrmTeamList;
use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\PositionStaff;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Service\LeaseServiceWithdraw;

class ConsoleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        //下属职员ids
        $timeType = timeType();
        $underStaffIds = array_unique(app(PositionStaff::class)->allUnderStaffIds(getUserId()));
        $staffs = Manager::query()->whereIn('id', $underStaffIds)->pluck('name', 'id');
        return $this->view('crm.console.index', compact('staffs','timeType'));
    }

    //数据简报
    public function brief(Request $request)
    {
        try {
            $defaultDay = [
                date("Y-m-d", strtotime("-1 day")),
                date("Y-m-d")
            ];
            $timeType = $request->get('timeType1');
            $dateRange = $request->dateRange;
            if ($timeType != 1 && $timeType != -1) {
                $defaultDay = [
                    date("Y-m-d", strtotime("-$timeType day")),
                    now()
                ];
            } else if ($timeType && $timeType == -1 && $dateRange) {
                $days = explode(" - ", $dateRange);
                $defaultDay = [
                    $days[0],
                    $days[1] . ' 23:59:59'
                ];
            }

            $result = [
                'new_cus_num'          => $this->getCusNum($request, $defaultDay),
                'new_contact_num'      => $this->getContactNum($request, $defaultDay),
                'new_fr_num'           => $this->getRecordNum($request, $defaultDay, CrmPlanRecord::TYPE_ONE),
                'new_fp_num'           => $this->getRecordNum($request, $defaultDay, CrmPlanRecord::TYPE_TWO),
                'lease_contract_num'   => $this->getLeaseContractNum($request, $defaultDay),
                'service_contract_num' => $this->getServiceContractNum($request, $defaultDay),
            ];

            return retArr('', ['data' => $result]);
        } catch (\Exception $exception) {
            Log::error('控制台数据简报错误：' . $exception);
            return retArr('系统异常', [], 500);
        }
    }

    public function getCusNum($request, $days)
    {
        $sql = CrmUser::query()->whereBetween('allotted_time', [strtotime($days[0]), strtotime($days[1])]);
        $type = $request->type ?? 'myself';
        if ($type == 'myself') {//自己客户
            $sql->where('charger_id', getUserId());
        } elseif ($type == 'under') {//下属客户
            //下属职员ids
            $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
            if (!empty($underStaffIds)) {
                $sql->whereIn('charger_id', array_unique($underStaffIds));
            } else {
                $sql->where("id", "<", 0);
            }
        } elseif ($type == 'myall') {
            //下属职员ids
            $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
            //带上当前登录人
            $underStaffIds[] = getUserId();
            $sql->whereIn('charger_id', array_unique($underStaffIds));
        } else {//具体某个下属的
            $sql->where('charger_id', $type);
        }
        return $sql->count();
    }

    public function getContactNum($request, $days)
    {
        $type = $request->type ?? 'myself';
        $sql = CrmContact::query()
            ->whereBetween('created_at', [strtotime($days[0]), strtotime($days[1])])
            ->whereHas('cus', function ($sql) use ($type) {
                if ($type == 'myself') {//自己客户
                    $sql->where('charger_id', getUserId());
                } elseif ($type == 'under') {//下属客户
                    //下属职员ids
                    $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
                    if (!empty($underStaffIds)) {
                        $sql->whereIn('charger_id', array_unique($underStaffIds));
                    } else {
                        $sql->where("id", "<", 0);
                    }
                } elseif ($type == 'myall') {
                    //下属职员ids
                    $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
                    //带上当前登录人
                    $underStaffIds[] = getUserId();
                    $sql->whereIn('charger_id', array_unique($underStaffIds));
                } else {//具体某个下属的
                    $sql->where('charger_id', $type);
                }
            });

        if ($type == 'myself') {//自己添加
            $sql->where('created_by', getUserId());
        } elseif ($type == 'under') {//下属客户
            //下属职员ids
            $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
            if (!empty($underStaffIds)) {
                $sql->whereIn('created_by', array_unique($underStaffIds));
            } else {
                $sql->where("id", "<", 0);
            }
        } elseif ($type == 'myall') {
            //下属职员ids
            $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
            //带上当前登录人
            $underStaffIds[] = getUserId();
            $sql->whereIn('created_by', array_unique($underStaffIds));
        } else {//具体某个下属的
            $sql->where('created_by', $type);
        }
        return $sql->count();
    }

    public function getRecordNum($request, $days, $recordType)
    {
        $type = $request->type ?? 'myself';
        $sql = CrmPlanRecord::query()->where('type', $recordType)
            ->whereBetween('created_at', [strtotime($days[0]), strtotime($days[1])])
            ->whereHas('cus', function ($sql) use ($type) {
                if ($type == 'myself') {//自己客户
                    $sql->where('charger_id', getUserId());
                } elseif ($type == 'under') {//下属客户
                    //下属职员ids
                    $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
                    if (!empty($underStaffIds)) {
                        $sql->whereIn('charger_id', array_unique($underStaffIds));
                    } else {
                        $sql->where("id", "<", 0);
                    }
                } elseif ($type == 'myall') {
                    //下属职员ids
                    $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
                    //带上当前登录人
                    $underStaffIds[] = getUserId();
                    $sql->whereIn('charger_id', array_unique($underStaffIds));
                } else {//具体某个下属的
                    $sql->where('charger_id', $type);
                }
            });

        if ($type == 'myself') {//自己客户
            $sql->where('created_by', getUserId());
        } elseif ($type == 'under') {//下属客户
            //下属职员ids
            $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
            if (!empty($underStaffIds)) {
                $sql->whereIn('created_by', array_unique($underStaffIds));
            } else {
                $sql->where("id", "<", 0);
            }
        } elseif ($type == 'myall') {
            //下属职员ids
            $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
            //带上当前登录人
            $underStaffIds[] = getUserId();
            $sql->whereIn('created_by', array_unique($underStaffIds));
        } else {//具体某个下属的
            $sql->where('created_by', $type);
        }
        return $sql->count();
    }

    public function getLeaseContractNum($request, $days)
    {
        $type = $request->type ?? 'myself';
        $sql = LeaseContract::query()->whereBetween('created_at', $days)
            ->whereHas('c_user', function ($sql) use ($type, $days) {
                if ($type == 'myself') {//自己客户
                    $sql->where('charger_id', getUserId());
                } elseif ($type == 'under') {//下属客户
                    //下属职员ids
                    $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
                    if (!empty($underStaffIds)) {
                        $sql->whereIn('charger_id', array_unique($underStaffIds));
                    } else {
                        $sql->where("id", "<", 0);
                    }
                } elseif ($type == 'myall') {
                    //下属职员ids
                    $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
                    //带上当前登录人
                    $underStaffIds[] = getUserId();
                    $sql->whereIn('charger_id', array_unique($underStaffIds));
                } else {//具体某个下属的
                    $sql->where('charger_id', $type);
                }
            });
        return $sql->count();
    }

    public function getServiceContractNum($request, $days)
    {
        $type = $request->type ?? 'myself';
        $sql = LeaseService::query()->whereBetween('created_at', $days)
            ->whereHas('b_user', function ($sql) use ($type, $days) {
                if ($type == 'myself') {//自己客户
                    $sql->where('charger_id', getUserId());
                } elseif ($type == 'under') {//下属客户
                    //下属职员ids
                    $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
                    if (!empty($underStaffIds)) {
                        $sql->whereIn('charger_id', array_unique($underStaffIds));
                    } else {
                        $sql->where("id", "<", 0);
                    }
                } elseif ($type == 'myall') {
                    //下属职员ids
                    $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
                    //带上当前登录人
                    $underStaffIds[] = getUserId();
                    $sql->whereIn('charger_id', array_unique($underStaffIds));
                } else {//具体某个下属的
                    $sql->where('charger_id', $type);
                }
            });
        return $sql->count();
    }



    //业绩趋势  折线图
    public function performanceTrend(Request $request) {
        try {

            $type = isset($request->type) ? $request->type : getUserId();
            $time_type = isset($request->time_type) ? $request->time_type :1;
            $performance_date = request('performance_date');

            //网点
            $data = LeaseService::selectRaw("DATE_FORMAT(lease_services.constract_begin_at, '%Y-%m-%d') as date,count(lease_services.id) as num")
                ->leftJoin('crm_users', 'crm_users.user_id', '=', 'lease_services.id')->whereIn('lease_services.status',[1])
                ->where("crm_users.cus_type", CrmUser::CUS_TYPE_TWO);
            //租点
            $list = LeaseContract::selectRaw("lease_contracts.created_date,count(lease_contracts.id) as num")
                ->leftJoin('crm_users', 'crm_users.user_id', '=', 'lease_contracts.user_id')->whereIn('lease_contracts.status',[3,7,8])
                ->where("crm_users.cus_type", CrmUser::CUS_TYPE_ONE);

            if ($time_type){
                $time =  selectTimeRange($time_type);
                if ($time){
                    $everyDate =  getDateRange($time['start_time'],$time['end_time']);
                    $data = $data->where('lease_services.constract_begin_at','>=',$time['start_time'])
                        ->where('lease_services.constract_begin_at','<=',$time['end_time']);

                    $list = $list->where('lease_contracts.created_date','>=',$time['start_time'])
                        ->where('lease_contracts.created_date','<=',$time['end_time']);
                }else{
                    $time_s_d = explode(' - ',$performance_date);

                    $everyDate =  getDateRange($time_s_d[0],$time_s_d[1]);
                    $data = $data->where('lease_services.constract_begin_at','>=',$time_s_d[0])
                        ->where('lease_services.constract_begin_at','<=',$time_s_d[1]);

                    $list = $list->where('lease_contracts.created_date','>=',$time_s_d[0])
                        ->where('lease_contracts.created_date','<=',$time_s_d[1]);
                }
            }

            if ($type == "myself"){
                $data =  $data->where('crm_users.charger_id', getUserId());
                $list =  $list->where('crm_users.charger_id', getUserId());
            }elseif ($type == 'under'){
                //下属职员ids
                $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
                if (!empty($underStaffIds)) {
                    $data = $data->whereIn('crm_users.charger_id', array_unique($underStaffIds));
                    $list = $list->whereIn('crm_users.charger_id', array_unique($underStaffIds));
                } else {
                    $data = $data->where("lease_services.id", "<", 0);
                    $list = $list->where("lease_contracts.id", "<", 0);
                }
            }elseif ($type == 'myall') {
                //下属职员ids
                $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
                //带上当前登录人
                $underStaffIds[] = getUserId();
                $data = $data->whereIn('crm_users.charger_id', array_unique($underStaffIds));
                $list = $list->whereIn('crm_users.charger_id', array_unique($underStaffIds));

            }else {//具体某个下属的
                $data = $data->where('crm_users.charger_id', $type);
                $list = $list->where('crm_users.charger_id', $type);
            }
            $data =  $data->groupBy("date")->orderBy("date")->pluck('num','date');
            $list =  $list->groupBy("lease_contracts.created_date")->orderBy("lease_contracts.created_date")->pluck('num','created_date');
            $returnData['days'][0] = "网点";
            $returnData['days'][1] = "用户";
            $returnData['hourArr'] = $everyDate;


            foreach ($everyDate as $value){
                if (isset($data[$value])){
                    $returnData['series'][0]['data'][] = $data[$value];
                }else{
                    $returnData['series'][0]['data'][] = 0;
                }

                if (isset($list[$value])){
                    $returnData['series'][1]['data'][] = $list[$value];
                }else{
                    $returnData['series'][1]['data'][] = 0;
                }

            }
            $returnData['series'][0]['name'] = '网点';
            $returnData['series'][0]['stack'] = '网点';
            $returnData['series'][0]['symbol'] = "circle";
            $returnData['series'][0]['symbolSize'] = 6;
            $returnData['series'][0]['type'] = "line";
            $returnData['series'][1]['name'] = '用户';
            $returnData['series'][1]['stack'] = '用户';
            $returnData['series'][1]['symbol'] = 'circle';
            $returnData['series'][1]['symbolSize'] = 6;
            $returnData['series'][1]['type'] = 'line';




            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }


    //业绩排行  柱状图
    public function performanceRank(Request $request) {
        try {


            $type = isset($request->type) ? $request->type : getUserId();
            $time_type = isset($request->time_type) ? $request->time_type :1;
            $performance_date = request('performance_date');

            //网点
            $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
            //带上当前登录人
            $underStaffIds[] = getUserId();
            $myAll = manager::selectRaw("id,name")
            ->whereIn('id',array_unique($underStaffIds))->get()->toArray();
            foreach ($myAll as $key => $value){
                $myAll[$key]['service_num'] = 0;
                $myAll[$key]['lease_num'] = 0;
                $myAll[$key]['total'] = 0;
            }



            $data = LeaseService::selectRaw("managers.id,managers.name,count(lease_services.id) as num")
                ->leftJoin('crm_users', 'crm_users.user_id', '=', 'lease_services.id')
                ->leftJoin('managers', 'managers.id', '=', 'crm_users.charger_id')
                ->whereIn('lease_services.status',[1])
                ->where("crm_users.cus_type", CrmUser::CUS_TYPE_TWO)->whereIn("crm_users.charger_id",  array_unique($underStaffIds));
            //租点
            $list = LeaseContract::selectRaw("managers.id,managers.name,count(lease_contracts.id) as num")
                ->leftJoin('crm_users', 'crm_users.user_id', '=', 'lease_contracts.user_id')
                ->leftJoin('managers', 'managers.id', '=', 'crm_users.charger_id')
                ->whereIn('lease_contracts.status',[3,7,8])
                ->where("crm_users.cus_type", CrmUser::CUS_TYPE_ONE)->whereIn("crm_users.charger_id",  array_unique($underStaffIds));

            if ($time_type){
                $time =  selectTimeRange($time_type);
                if ($time){
                    $data = $data->where('lease_services.constract_begin_at','>=',$time['start_time'])
                        ->where('lease_services.constract_begin_at','<=',$time['end_time']);

                    $list = $list->where('lease_contracts.created_date','>=',$time['start_time'])
                        ->where('lease_contracts.created_date','<=',$time['end_time']);
                }else{
                    $time_s_d = explode(' - ',$performance_date);
                    $data = $data->where('lease_services.constract_begin_at','>=',$time_s_d[0])
                        ->where('lease_services.constract_begin_at','<=',$time_s_d[1]);

                    $list = $list->where('lease_contracts.created_date','>=',$time_s_d[0])
                        ->where('lease_contracts.created_date','<=',$time_s_d[1]);
                }
            }


            $data =  $data->groupBy("crm_users.charger_id")->pluck('num','managers.id')->toArray();


            $list =  $list->groupBy("crm_users.charger_id")->pluck('num','managers.id')->toArray();



            foreach ($myAll as $k=>$v){
                if (isset($data[$v['id']])){
                    $myAll[$k]['service_num'] = $data[$v['id']];
                }else{
                    $data[$v['id']] = 0;
                }

                if (isset($list[$v['id']])){
                    $myAll[$k]['lease_num'] = $list[$v['id']];
                }else{
                    $list[$v['id']] = 0;
                }
                $myAll[$k]['total'] = $list[$v['id']] + $data[$v['id']];
            }

        $last_names = array_column($myAll,'total');
        array_multisort($last_names,SORT_DESC,$myAll);


        foreach ($myAll as $k=>$v){
            $returnData[$k]['网点'] = $v['service_num'];
            $returnData[$k]['用户'] = $v['lease_num'];
            $returnData[$k]['name'] = $v['name'];

        }

            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }
}
