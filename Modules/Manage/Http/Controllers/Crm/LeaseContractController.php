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
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Crm\CrmOpenSea;
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
use Modules\Manage\Transformers\CrmCustomerResource;

class LeaseContractController extends Controller
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
//        $data = app(CrmRuleSetting::Class)->pluck('json', 'type')->toArray();
//        $data_1 = $data_2 = [];

//        if (!empty($data[1])) $data_1 = json_decode($data[1], true);
//        if (!empty($data[2])) $data_2 = json_decode($data[2], true);
        $water = app(CrmOpenSea::Class)->get()->toArray();
        $provinces = allUserProvinces();

        return $this->view("lease.crm.lease_contract",compact("provinces"),[
//            'data_1' => $data_1,
//            'data_2' => $data_2,
            'water' => $water
        ]);
    }


    public function leaseContractSearch(Request $request)
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
        $type = isset($request->appid) ? $request->appid : 1;
        $name = isset($request->name) ? $request->name : '';
        $status = isset($request->status) ? $request->status : 0;
        $constract_begin_at = isset($request->constract_begin_at) ? $request->constract_begin_at : 0;
        $constract_end_at = isset($request->constract_end_at) ? $request->constract_end_at : 0;
        $province_id = isset($request->province_id) ? $request->province_id : 0;


        $page = $pageNum - 1;
        if ($page != 0) {
            $page = $limit * $page;

        }

        $object_count = app(LeaseContract ::class)
            ->leftJoin("crm_users", "lease_contracts.user_id", "=", "crm_users.user_id")
            ->with('b_user')
            ->where('crm_users.cus_type', '=', 1)->select("crm_users.id as ids","crm_users.id as ids","lease_contracts.contract_no", "lease_contracts.status", "lease_contracts.model_name", "lease_contracts.rentals",
                "lease_contracts.prepayment", "lease_contracts.payment_payed_at", "lease_contracts.effected_at", "lease_contracts.lease_expired_at", "lease_contracts.user_nickname", "lease_contracts.service_name",
                "crm_users.charger_name","lease_contracts.service_province_name","lease_contracts.service_city_name","lease_contracts.lease_term","lease_contracts.lease_unit", "lease_contracts.user_mobile", "lease_contracts.service_id");

        if ($status) {
            if ($status != 1){
                $object_count = $object_count->where('lease_contracts.status','!=', 3);
            }else{
                $object_count = $object_count->where('lease_contracts.status', 3);
            }
        }

        if(!empty($constract_begin_at)){
            $time = explode(' - ',$constract_begin_at);

            $object_count = $object_count->where('lease_contracts.effected_at', '>=',$time[0])
                ->where('lease_contracts.effected_at', '<=',$time[1]);

        }

        if(!empty($constract_end_at)){
            $time_end = explode(' - ',$constract_end_at);

            $object_count = $object_count->where('lease_contracts.contract_expired_at', '>=',$time_end[0])
                ->where('lease_contracts.contract_expired_at', '<=',$time_end[1]);

        }


        if ($type == 1) {//自己客户联系人

            $object_count->where('crm_users.charger_id', getUserId());

        } elseif ($type == 3) {//下属客户联系人
            //下属职员ids
            $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());

            if (!empty($underStaffIds)) {
                $object_count->whereIn('crm_users.charger_id', array_unique($underStaffIds));
            } else {
                $object_count->where("crm_users.id", "<", 0);
            }
        } elseif ($type == 2) {
                $customer_ids  =  app(CrmTeamList::class)->where('user_id',getUserId())->pluck('customer_id')->toArray();
                $object_count->whereIn('lease_contracts.user_id', $customer_ids);

        } elseif ($type == 4) {
                $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());

                if (!empty($underStaffIds)) {
                    $customer_ids  =  app(CrmTeamList::class)->whereIn('user_id', array_unique($underStaffIds))->pluck('customer_id')->toArray();
                    $object_count->whereIn('lease_contracts.user_id', $customer_ids);
                } else {
                    $object_count->where("crm_users.id", "<", 0);
                }
        }

        if ($province_id) {
            $object_count = $object_count->where('lease_contracts.province_id', $province_id);
        }

        if ($name) {

            $object_count = $object_count->where(function ($query) use ($name) {
                $query->where('lease_contracts.service_name', 'like', '%' . $name . '%')->orWhere('lease_contracts.contract_no', 'like', '%' . $name . '%')
                    ->orWhere('crm_users.charger_name', 'like', '%' . $name . '%')->orWhere('lease_contracts.user_nickname', 'like', '%' . $name . '%')
                    ->orWhere('lease_contracts.user_mobile', 'like', '%' . $name . '%');
            });
        }

        $count = $object_count->count();

        $list = $object_count->offset($page)->limit($limit)->orderBy('lease_contracts.created_at', 'desc')->get()->toArray();

//        dd($list);
        $data['list'] = $list;
        $data['count'] = $count;

        return $data;
    }


    public function leaseContractDetail(Request $request)
    {
        $id = $request->id;
        $model = $this->repository->with('createUser')->find($id);
        $model = CrmCustomerResource::transformers($model);
        return $this->view('lease.crm.sea_customer_detail', compact('model'));

    }

    public function seaCustomerClaim(Request $request)
    {
        $id = getUserId();
        $name = getUserName();
        $customer_id = $request->customer_id;

        if (empty($customer_id)) return result("", 0);


        $customer_ids = explode(",", $customer_id);
        CrmUser::whereIn("id", $customer_ids)->update(['charger_id' => $id, 'charger_name' => $name]);


        return result("", 1, [], []);

    }

    public function seaCustomerDistributeView(Request $request)
    {
        $customer_id = $request->customer_id;
        $hasIds = CrmSeaStaff::where("sea_id", $request->sea_id)->pluck("staff_id")->toArray();
        $list = Manager::query()->whereNotIn("id", $hasIds)->orderBy("id", "desc")->get();

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
        $name = Manager::where('id', $user_id)->first()->toArray();
        $customer_ids = explode(",", $customer_id);
        CrmUser::whereIn("id", $customer_ids)->update(['charger_id' => $user_id, 'charger_name' => $name['name']]);

    }


    public function seaCustomerTransferView(Request $request)
    {
        $customer_id = $request->customer_id;
        $list = app(CrmOpenSea::Class)->get();
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

            $charger_array = CrmUser::where('id', $customer_id)->where('charger_id', $id)->first()->toArray();
            if (empty($charger_array) || $id == $user_id) {
                return result("", 0);
            }

            $is_del = CrmTeamList::where('customer_id', $customer_id)->where('user_id', $user_id)->delete();
            if ($is_del) {
                return result("", 1);
            }

        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }

    }


    public function seaCustomerTeamAdd($customer_id)
    {
        try {
            $crm_team_list = CrmTeamList::where('customer_id', $customer_id)->pluck("user_id")->toArray();

            $charger_array = CrmUser::where('id', $customer_id)->pluck('charger_id')->toArray();

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


        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }

    }
}
