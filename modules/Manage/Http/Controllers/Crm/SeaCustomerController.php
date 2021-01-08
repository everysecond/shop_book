<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/8 11:36
 */

namespace Modules\Manage\Http\Controllers\Crm;

use App\Models\Manager;
use Doctrine\DBAL\Schema\AbstractAsset;
use \Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Crm\CrmOpenSea;
use Modules\Manage\Models\Crm\CrmOperateLog;
use Modules\Manage\Models\Crm\CrmPlanRecord;
use Modules\Manage\Models\Crm\CrmRuleSetting;
use Modules\Manage\Models\Crm\CrmSeaStaff;
use Modules\Manage\Models\Crm\CrmTeamList;
use Modules\Manage\Models\Crm\CrmUser;

use Modules\Manage\Models\PositionStaff;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Service\LeaseIncomeLog;
use Modules\Manage\Models\Service\LeaseServiceWithdraw;
use Modules\Manage\Repositories\Crm\CustomerRepository;
use Modules\Manage\Repositories\Report\LeaseServiceRepository;
use Modules\Manage\Services\ChartService;
use Modules\Manage\Services\UploadService;
use Modules\Manage\Transformers\CrmCustomerResource;

class SeaCustomerController extends Controller
{
    protected $repository;
    protected $chartService;
    protected $incomeModel;
    protected $leaseServiceWithdraw;

    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;

    }


    public function index()
    {


        $sea_ids = app(CrmSeaStaff::class)->where('staff_id', getUserId())->pluck('sea_id');
        if ($sea_ids) {
            $water = app(CrmOpenSea::Class)->whereIn('id', $sea_ids)->get()->toArray();
        }

        $is_claim = app(CrmSeaStaff::class)->where('staff_id', getUserId())->pluck('can_get', 'sea_id');
        $is_distribute = app(CrmSeaStaff::class)->where('staff_id', getUserId())->pluck('can_assign', 'sea_id');

        $is_transfer = count($sea_ids);
        $provinces = allUserProvinces();

        return $this->view("lease.crm.sea_customer", compact("provinces"), [
            'water' => $water,
            'is_claim' => $is_claim,
            'is_distribute' => $is_distribute,
            'is_transfer' => $is_transfer
        ]);
    }


    public function seaCustomerSearch(Request $request)
    {
        try {


            $list = $this->getCustomerList($request, "lists");
            $data = $list['list'];
            $count = $list['count'];


            return result("", 0, $data, $count);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }


    public function getCustomerList($request, $type = "lists")
    {

        $pageNum = isset($request->page) ? $request->page : 0;
        $limit = isset($request->limit) ? $request->limit : 10;
        $appid = isset($request->appid) ? $request->appid : 1;
        $name = isset($request->name) ? $request->name : '';
        $history_deal = isset($request->history_deal) ? $request->history_deal : 0;
        $cus_source = isset($request->cus_source) ? $request->cus_source : 0;
        $cus_level = isset($request->cus_level) ? $request->cus_level : 0;
        $province_id = isset($request->province_id) ? $request->province_id : 0;
        $created_at = isset($request->created_at) ? $request->created_at : 0;
//        $where['sea_type'] = $appid;
//
//        $where['charger_id'] = ['>', 0];

        $page = $pageNum - 1;
        if ($page != 0) {
            $page = $limit * $page;

        }

        $object_count = app(CrmUser ::class)->where('charger_id', '=', 0);
        if ($appid) {
            $object_count = $object_count->where('sea_type', $appid);
        }

        if ($history_deal) {
            $object_count = $object_count->where('history_deal', $history_deal);
        }

        if ($cus_source) {
            $object_count = $object_count->where('cus_source', $cus_source);
        }

        if ($cus_level) {
            $object_count = $object_count->where('cus_level', $cus_level);
        }
        if ($province_id) {
            $object_count = $object_count->where('province_id', $province_id);
        }

        if(!empty($created_at)){
            $time = explode(' - ',$created_at);

            $object_count = $object_count->where('created_at', '>=',$time[0])
                ->where('created_at', '<=',$time[1]);

        }

        if ($name) {

            $object_count = $object_count->where(function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%')->orWhere('mobile', 'like', '%' . $name . '%');
            });
        }

        $count = $object_count->count();

        $list = $object_count->offset($page)->limit($limit)->orderBy('created_at', 'desc')->get()->toArray();

        $data['list'] = $list;
        $data['count'] = $count;

        return $data;
    }


    public function seaCustomerDetail(Request $request)
    {


        $id = $request->id;
        $model = $this->repository->with('createUser', 'preFollow', 'nextFollow')->find($id);
        $model = CrmCustomerResource::transformers($model);
        $logs = CrmOperateLog::query()->where([
            'resource_id' => $id,
            'table_name'  => 'crm_users'
        ])
            ->with('createdUser', 'targetUser')
            ->orderBy('created_at', 'desc')
            ->get();

        $follows = CrmPlanRecord::query()->where('cus_id', $id)->with('images', 'contact', 'createdUser')
            ->orderBy('follow_at', 'desc')->get();
        $follows = $this->transferFollows($follows);
        $right = cdtRigths($id);

        return $this->view('lease.crm.sea_customer_detail', compact('model', 'logs', 'follows'), [
            'distributes'=>$right['can_assign'],
            'claims'=>$right['can_get'],
        ]);


    }

    public function seaCustomerClaim(Request $request)
    {
//        $customer_id = $request->customer_id;
//        $sea_ids = app(CrmSeaStaff::class)->where('staff_id',getUserId())->pluck('sea_id');


        $id = getUserId();
        $name = getUserName();
        $customer_id = $request->customer_id;

        if (empty($customer_id)) return result("", 0);


        $customer_ids = explode(",", $customer_id);

        CrmUser::whereIn("id", $customer_ids)->update(['charger_id' => $id, 'charger_name' => $name, 'sea_type' => 0,'allotted_time'=>time()]);

        foreach ($customer_ids as $cus) {
            $logs[] = [
                'resource_id' => $cus,
                'content'     => "",
                'type'        => "认领客户"
            ];
        }
        app(CrmOperateLog::class)->batchInsert('crm_users', '', $logs);

        return result("", 1, [], []);

    }

    public function seaCustomerDistributeView(Request $request)
    {

        $customer_id = $request->customer_id;
        $hasIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());

//        $hasIds = CrmSeaStaff::where("sea_id", $request->sea_id)->pluck("staff_id")->toArray();
        $list = Manager::query()->whereIn("id", $hasIds)->orderBy("id", "desc")->get();

        $options = ["" => ""];
        foreach ($list as $item) {
            $options[$item->id] = $item->name . "($item->mobile)";
        }

        $positions = CrmOpenSea::query()->pluck("name", "id");
        $positions->prepend('', '');

        return $this->view('lease.crm.sea_distribute', ['customer_id' => $customer_id], compact('options', 'positions'));

    }


    public function seaCustomerDistribute(Request $request)
    {
        $user_id = $request->staff_id;
        $customer_id = $request->customer_id;


        if (empty($customer_id)) return result("", 0);
        $name = Manager::where('id', $user_id)->first();

        $customer_ids = explode(",", $customer_id);
        CrmUser::whereIn("id", $customer_ids)->update(['charger_id' => $user_id, 'charger_name' => $name->name, 'sea_type' => 0,'allotted_time'=>time()]);
        foreach ($customer_ids as $cus) {
            $logs[] = [
                'resource_id' => $cus,
                'content'     => "",
                'type'        => "分配客户给",
                'target_user_id'=>$user_id
            ];
        }
        app(CrmOperateLog::class)->batchInsert('crm_users', '', $logs);
        return result("", 1, [], []);
    }


    public function seaCustomerTransferView(Request $request)
    {

        $customer_id = $request->customer_id;

        $sea_ids = app(CrmSeaStaff::class)->where('staff_id', getUserId())->pluck('sea_id');
        if ($sea_ids) {
            $list = app(CrmOpenSea::Class)->whereIn('id', $sea_ids)->get();
        }
//        $list = app(CrmOpenSea::Class)->get();

        $options = ["" => ""];
        foreach ($list as $item) {
            $options[$item->id] = $item->name;
        }
        return $this->view('lease.crm.sea_transfer', ['customer_id' => $customer_id], compact('options', 'positions'));

    }


    public function seaCustomerTransfer(Request $request)
    {
        $water_id = $request->water_id;
        $customer_id = $request->customer_id;


        if (empty($customer_id)) return result("", 0);


        $customer_ids = explode(",", $customer_id);
        CrmUser::whereIn("id", $customer_ids)->update(['sea_type' => $water_id]);
        $water_name =  CrmOpenSea::query()->pluck("name", "id");

        foreach ($customer_ids as $cus) {
            $logs[] = [
                'resource_id' => $cus,
                'content'     => "",
                'type'        => "移入公海",
                'content' =>$water_name[$water_id]
            ];
        }
        app(CrmOperateLog::class)->batchInsert('crm_users', '', $logs);
        return result("", 1, [], []);

    }

    public function seaCustomerTeam($customer_id)
    {
        try {
            //协作人员
            $crm_team_list = CrmTeamList::where('customer_id', $customer_id)->get()->toArray();
            $user_ids = array_column($crm_team_list, 'user_id');
            $mobile_name = Manager::whereIn('id', $user_ids)->get()->toArray();

            $position = PositionStaff::leftJoin('positions', 'positions.id', '=', 'position_staff.position_id')
                ->whereIn('position_staff.staff_id', $user_ids)->get()->toArray();

            foreach ($crm_team_list as $k1 => $v1) {
                $crm_team_list[$k1]['position'] = $crm_team_list[$k1]['mobile'] = $crm_team_list[$k1]['name'] = '';
                foreach ($position as $k2 => $v2) {
                    if ($v1['user_id'] == $v2['staff_id']) {
                        $crm_team_list[$k1]['position'] .= $v2['title'] . ";";
                    }

                }
                foreach ($mobile_name as $k3 => $v3) {
                    if ($v1['user_id'] == $v3['id']) {
                        $crm_team_list[$k1]['mobile'] = $v3['mobile'];
                        $crm_team_list[$k1]['name'] = $v3['name'];
                    }

                }
            }
            $charger_array = array();
            //负责人
            $charger_array = CrmUser::where('id', $customer_id)->get('charger_id')->toArray();
            if (!empty($charger_array)) {
                $charger_mobile_name = Manager::where('id', $charger_array[0]['charger_id'])->get()->toArray();
                $charger_position = PositionStaff::leftJoin('positions', 'positions.id', '=', 'position_staff.position_id')
                    ->where('position_staff.staff_id', $charger_array[0]['charger_id'])->get()->toArray();

                foreach ($charger_array as $k1 => $v1) {
                    $charger_array[$k1]['user_id'] = $charger_array[$k1]['position'] = $charger_array[$k1]['mobile'] = $charger_array[$k1]['name'] = '';
                    $charger_array[$k1]['team_role'] = 1;
                    foreach ($charger_position as $k2 => $v2) {
                        if ($v1['charger_id'] == $v2['staff_id']) {
                            $charger_array[$k1]['position'] .= $v2['title'] . ";";
                        }

                    }
                    foreach ($charger_mobile_name as $k3 => $v3) {
                        if ($v1['charger_id'] == $v3['id']) {
                            $charger_array[$k1]['mobile'] = $v3['mobile'];
                            $charger_array[$k1]['name'] = $v3['name'];
                            $charger_array[$k1]['user_id'] = $v1['charger_id'];
                        }

                    }
                }

            }
            $list = array_merge($charger_array, $crm_team_list);

            return result("", 0, $list);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }

    }


    public function seaCustomerTeamDel(Request $request, $customer_id)
    {
        try {
            $user_id = $request->user_id;
            $id = getUserId();

            $charger_array = CrmUser::where('id', $customer_id)->where('charger_id', $id)->first();
            if (empty($charger_array) || $id == $user_id) {
                return result("", 0);
            }

            $is_del = CrmTeamList::where('customer_id', $customer_id)->where('user_id', $user_id)->delete();
            if ($is_del) {
                $logs[] = [
                    'resource_id' => $customer_id,
                    'content'     => "",
                    'type'        => "移出团队",
                    'target_user_id'=>$user_id

                ];
                app(CrmOperateLog::class)->batchInsert('crm_users', '', $logs);

                return result("", 1);
            }

        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }

    }


    public function seaCustomerTeamAdd($customer_id)
    {
        try {
            $crm_team_list = CrmTeamList::where('customer_id', $customer_id)->pluck("user_id");
            if ($crm_team_list) $crm_team_list = [];
            $charger_array = CrmUser::where('id', $customer_id)->pluck('charger_id');
            if ($charger_array) $charger_array = [];
            $new_array = array_merge($crm_team_list, $charger_array);

            $list = Manager::query()->orderBy("id", "desc")->get();

            $options = ["" => ""];
            foreach ($list as $item) {
                if (in_array($item->id, $new_array)) continue;
                $options[$item->id] = $item->name . "($item->mobile)";
            }

            return $this->view('lease.crm.sea_team_add', ['customer_id' => $customer_id], compact('options', 'positions'));

        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }

    }

    public function seaCustomerTeamCreate(Request $request)
    {
        try {
            $user_id = $request->staff_id;
            $customer_id = $request->customer_id;
            $data = [
                'customer_id' => $customer_id,
                'user_id' => $user_id,
                'team_role' => 2,
                'created_at' => time()
            ];
            CrmTeamList::insert($data);


            $logs[] = [
                'resource_id' => $customer_id,
                'content'     => "",
                'type'        => "加入团队",
                'target_user_id'=>$user_id

            ];


            app(CrmOperateLog::class)->batchInsert('crm_users', '', $logs);


        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }

    }


    public function seaCustomerContract($customer_id, Request $request)
    {
        try {
            $id = $request->id;
            if ($id == 1) {
                $list = $this->getLeaseContractList($customer_id, "lists");

            } elseif ($id == 2) {

                $list = $this->getSerciceContractList($customer_id, "lists");

            }


            return result("", 0, $list);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }

    }


    public function getLeaseContractList($customer_id, $type = "lists")
    {

        $object_count = app(LeaseContract ::class)->leftJoin("crm_users", "lease_contracts.user_id", "=", "crm_users.user_id")
            ->where('crm_users.cus_type', '=', 1)->select("crm_users.id as ids", "lease_contracts.contract_no", "lease_contracts.status", "lease_contracts.model_name", "lease_contracts.rentals",
                "lease_contracts.prepayment", "lease_contracts.payment_payed_at", "lease_contracts.effected_at", "lease_contracts.contract_expired_at", "lease_contracts.user_nickname", "lease_contracts.service_name",
                "crm_users.charger_name", "lease_contracts.service_province_name", "lease_contracts.service_city_name", "lease_contracts.lease_term", "lease_contracts.lease_unit", "lease_contracts.user_mobile");
        $object_count->where('lease_contracts.user_id', $customer_id);
        $list = $object_count->orderBy('lease_contracts.created_at', 'desc')->get()->toArray();
        return $list;
    }


    public function getSerciceContractList($customer_id, $type = "lists")
    {

        $object_count = app(LeaseService ::class)->leftJoin("crm_users", "lease_services.id", "=", "crm_users.user_id")
            ->where('crm_users.cus_type', '=', 2)->select("crm_users.id as ids", "lease_services.id", "lease_services.status", "lease_services.audited_at", "lease_services.constract_begin_at",
                "lease_services.constract_end_at", "lease_services.league", "lease_services.bail", "lease_services.service_name", "lease_services.province_name", "lease_services.city_name",
                "crm_users.charger_name");
        $object_count->where('lease_services.id', $customer_id);

        $list = $object_count->orderBy('lease_services.created_at', 'desc')->get()->toArray();
        return $list;
    }

    protected function transferFollows($follows)
    {
        $mode = dictArrAll('crm_follow_mode');
        $managers = allUsersArr();
        foreach ($follows as &$follow) {
            $follow['mode'] = $mode[$follow->follow_mode] ?? '';
            if ($ids = $follow->follow_user_ids) {
                $ids = explode(',', $ids);
                $follow['follow_users'] = '';
                foreach ($ids as $id) {
                    $name = $managers[$id]['name'];
                    $follow['follow_users'] .= $follow['follow_users'] ? ',' . $name : $name;
                }
            } else {
                $follow['follow_users'] = '';
            }
        }
        return $follows;
    }

    public function export(Request $request)
    {
        return app(UploadService::class)->downSeaCusExcel($request);
    }

}
