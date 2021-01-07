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
use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Service\LeaseIncomeLog;
use Modules\Manage\Models\Service\LeaseServiceWithdraw;
use Modules\Manage\Repositories\Crm\CustomerRepository;
use Modules\Manage\Repositories\Report\LeaseServiceRepository;
use Modules\Manage\Services\ChartService;
use Modules\Manage\Transformers\CrmCustomerResource;

class ServiceContractController extends Controller
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

        $sea_ids = app(CrmSeaStaff::class)->where('staff_id',getUserId())->pluck('sea_id');
        if ($sea_ids){
            $water = app(CrmOpenSea::Class)->whereIn('id', $sea_ids)->get()->toArray();
        }

        $provinces = allUserProvinces();

        return $this->view("lease.crm.service_contract",compact("provinces"),[
            'water' => $water
        ]);
    }


    public function serviceContractSearch(Request $request)
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

        $object_count = app(LeaseService ::class)->leftJoin("crm_users", "lease_services.id", "=", "crm_users.user_id")
            ->where('crm_users.cus_type', '=', 2)->select("crm_users.id as ids","lease_services.id", "lease_services.status", "lease_services.audited_at", "lease_services.constract_begin_at",
                "lease_services.constract_end_at", "lease_services.league", "lease_services.bail", "lease_services.service_name", "lease_services.province_name", "lease_services.city_name",
                "crm_users.charger_name");


        if ($status) {
            if ($status != 1){
                $object_count = $object_count->where('lease_services.status','!=', 1);
            }else{
                $object_count = $object_count->where('lease_services.status', $status);
            }
        }

        if(!empty($constract_begin_at)){
            $time = explode(' - ',$constract_begin_at);

            $object_count = $object_count->where('lease_services.constract_begin_at', '>=',$time[0])
                ->where('lease_services.constract_begin_at', '<=',$time[1]);

        }

        if(!empty($constract_end_at)){
            $time_end = explode(' - ',$constract_end_at);

            $object_count = $object_count->where('lease_services.constract_end_at', '>=',$time_end[0])
                ->where('lease_services.constract_end_at', '<=',$time_end[1]);

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
                $object_count->whereIn('lease_services.id', $customer_ids);

        } elseif ($type == 4) {
                $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());

                if (!empty($underStaffIds)) {
                    $customer_ids  =  app(CrmTeamList::class)->whereIn('user_id', array_unique($underStaffIds))->pluck('customer_id')->toArray();
                    $object_count->whereIn('lease_services.id', $customer_ids);
                } else {
                    $object_count->where("crm_users.id", "<", 0);
                }
        }

        if ($province_id) {
            $object_count = $object_count->where('lease_services.province_id', $province_id);
        }

        if ($name) {

            $object_count = $object_count->where(function ($query) use ($name) {
                $query->where('lease_services.service_name', 'like', '%' . $name . '%')->orWhere('lease_services.id', 'like', '%' . $name . '%')
                    ->orWhere('crm_users.charger_name', 'like', '%' . $name . '%');
            });
        }

        $count = $object_count->count();

        $list = $object_count->offset($page)->limit($limit)->orderBy('lease_services.created_at', 'desc')->get()->toArray();

//        dd($list);
        $data['list'] = $list;
        $data['count'] = $count;

        return $data;
    }


}
