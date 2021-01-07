<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/30 11:40
 */

namespace Modules\Manage\Http\Controllers\Crm;

use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Lease\Models\BlAgent;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Crm\CrmContact;
use Modules\Manage\Models\Crm\CrmOpenSea;
use Modules\Manage\Models\Crm\CrmOperateLog;
use Modules\Manage\Models\Crm\CrmPlanRecord;
use Modules\Manage\Models\Crm\CrmTeamList;
use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\PositionStaff;
use Modules\Manage\Repositories\Crm\CustomerRepository;
use Modules\Manage\Services\UploadService;
use Modules\Manage\Transformers\CrmCustomerResource;

class CustomerController extends Controller
{
    protected $repository;

    protected $cusTypeArr = [
        CrmUser::CUS_TYPE_ONE   => '租点用户',
        CrmUser::CUS_TYPE_TWO   => '租点网点',
        CrmUser::CUS_TYPE_THREE => '快点用户',
        CrmUser::CUS_TYPE_FOUR  => '快点网点'
    ];

    protected $cusLevelArr = [
        CrmUser::CUS_LEVEL_ONE   => '重点客户',
        CrmUser::CUS_LEVEL_TWO   => '普通客户',
        CrmUser::CUS_LEVEL_THREE => '非优先客户'
    ];

    public function __construct(CustomerRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    public function down(Request $request)
    {
        return app(UploadService::class)->downCusExcel($request);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return $this->view('crm.customer.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $provinces = allLeaseProvinces();
        $allUnderPositionIds = app(PositionStaff::class)->allUnderStaffIds(getUserId(), true);
        $list = Manager::query()->whereIn('id', $allUnderPositionIds)->orderBy("id", "desc")->get();
        $managers = ["" => ""];
        foreach ($list as $item) {
            $managers[$item->id] = $item->name;
        }

        return $this->view('crm.customer.create', compact('provinces', 'managers'));
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name'        => 'required',
            'cus_type'    => 'required|int',
            'cus_level'   => 'required|int',
            'mobile'      => "required|string|unique:crm_users,mobile,,,cus_type," . $request->cus_type,
            'charger_id'  => 'required',
            'short_name'  => 'max:100',
            'province_id' => '',
            'city_id'     => '',
            'county_id'   => '',
            'address'     => 'max:255',
            'memo'        => ''
        ], [], CrmUser::FIELD_MEANS);
        $provinces = allAgentsArr();
        if ($data['cus_type'] == CrmUser::CUS_TYPE_TWO) $data['status'] = 0;
        $data['cus_source'] = CrmUser::CUS_SOURCE_THREE;
        $data['created_by'] = getUserId();
        $data['allotted_time'] = time();
        $data['province_name'] = $data['province_id'] && isset($provinces[$data['province_id']]) ? $provinces[$data['province_id']] : '';
        $data['city_name'] = $data['city_id'] && isset($provinces[$data['city_id']]) ? $provinces[$data['city_id']] : '';
        $data['county_name'] = $data['county_id'] && isset($provinces[$data['county_id']]) ? $provinces[$data['county_id']] : '';
        $allUsersArr = allUsersArr();
        $data['charger_name'] = isset($allUsersArr[$data['charger_id']]) ? $allUsersArr[$data['charger_id']]['name'] : '';
        $data['area'] = $data['province_name'] . $data['city_name'];
        $this->repository->create(removeNullValue($data));
        return $this->success("添加成功");
    }

    public function detail($id)
    {
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
        return $this->view('crm.customer.detail', compact('model', 'logs', 'follows'));
    }

    public function paginate(Request $request)
    {
        $result = $this->repository->skipCriteria()->scopeQuery(function ($query) use ($request) {
            if ($k = $request->cus_type) $query = $query->where('cus_type', $k);
            if ($k = $request->cus_level) $query = $query->where('cus_level', $k);
            if ($k = $request->cus_source) $query = $query->where('cus_source', $k);
            if ($k = $request->history_deal) $query = $query->where('history_deal', $k);
//            dd($query->toSql());
            if ($k = $request->province_id) $query = $query->where('province_id', $k);
            if ($k = $request->date1) {
                $dateArr = explode(' - ', $k);
                if (isset($dateArr[1])) {
                    $dateArr[1] = $dateArr[1] . ' 23:59:59';
                } else {
                    $dateArr[1] = now();
                }
                $query = $query->whereBetween('created_at', $dateArr);
            };

            if ($k = $request->date2) {
                $dateArr = explode(' - ', $k);
                if (isset($dateArr[1])) {
                    $dateArr[1] = $dateArr[1] . ' 23:59:59';
                } else {
                    $dateArr[1] = now();
                }
                $query = $query->whereHas('contract', function ($query) use ($dateArr) {
                    $query->whereBetween('lease_expired_at', $dateArr);
                });
            };

            if ($k = $request->date3) {
                $dateArr = explode(' - ', $k);
                if (isset($dateArr[1])) {
                    $dateArr[1] = $dateArr[1] . ' 23:59:59';
                } else {
                    $dateArr[1] = now();
                }
                $query = $query->whereBetween('constract_end_at', $dateArr);
            };


            if ($search = $request->searchStr) {
                $query = $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%")
                        ->orWhere('charger_name', 'like', "%$search%");
                });
            }
            $type = $request->type ?? 'myself';
            if ($type == 'myself') {//自己客户
                $query = $query->where('charger_id', getUserId());
            } elseif ($type == 'under') {//下属客户
                //下属职员ids
                $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
                if (!empty($underStaffIds)) {
                    $query = $query->whereIn('charger_id', array_unique($underStaffIds));
                } else {
                    $query = $query->where("id", "<", 0);
                }
            } elseif ($type == 'myteam') {
                $teamCus = CrmTeamList::query()->where([
                    'user_id'   => getUserId(),
                    'team_role' => CrmTeamList::ROLE_TWO
                ])->pluck('customer_id')->toArray();
                $query = $query->whereIn('id', array_unique($teamCus))->where('charger_id', '!=', getUserId());
            } elseif ($type == 'underteam') {
                //下属职员ids
                $underStaffIds = array_unique(app(PositionStaff::class)->allUnderStaffIds(getUserId()));
                if (!empty($underStaffIds)) {
                    $teamCus = CrmTeamList::query()->where('team_role', CrmTeamList::ROLE_TWO)
                        ->whereIn('user_id', $underStaffIds)->pluck('customer_id')->toArray();
                    $query = $query->whereIn('id', array_unique($teamCus));
                } else {
                    $query = $query->where("id", "<", 0);
                }
            }
            return $query->orderByRaw('FIELD(is_top,1,0),FIELD(is_mark,1,0),id desc,created_at desc');
        })->with('contact', 'preFollow', 'contract')->paginate(request('limit'));
        return CrmCustomerResource::collection($result);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function show()
    {
        $data = Position::query()->where("pid", 0)
            ->with('children')->orderBy("sort", "desc")->get()->toArray();
        return $this->success('', $this->formateList($data));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $provinces = allLeaseProvinces();
        $allUnderPositionIds = app(PositionStaff::class)->allUnderStaffIds(getUserId(), true);
        $list = Manager::query()->whereIn('id', $allUnderPositionIds)->orderBy("id", "desc")->get();
        $managers = ["" => ""];
        foreach ($list as $item) {
            $managers[$item->id] = $item->name . "($item->mobile)";
        }
        $model = $this->repository->find($id);
        $cusTypes = $this->cusTypeArr;
        $cusLevels = $this->cusLevelArr;

        return $this->view('crm.customer.edit', compact('model', 'provinces', 'managers', 'cusTypes', 'cusLevels'));
    }

    /**
     * 只能修改自己客户的联系人
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $old = $this->repository->scopeQuery(function ($query) {
            return $query->where('charger_id', getUserId());
        })->find($id);
        if (!$old) return $this->error("无权限修改");
        $transfer = CrmUser::FIELD_MEANS;
        $data = $this->validate($request, [
            'name'        => 'required',
            'cus_level'   => 'required|int',
            'mobile'      => "required|string|unique:crm_users,mobile,$id,id,cus_type," . $request->cus_type,
            'short_name'  => 'max:100',
            'province_id' => '',
            'city_id'     => '',
            'county_id'   => '',
            'address'     => 'max:255',
            'memo'        => ''
        ], [], $transfer);
        $provinces = allAgentsArr();
        $data['province_name'] = $data['province_id'] && isset($provinces[$data['province_id']]) ? $provinces[$data['province_id']] : '';
        $data['city_name'] = $data['city_id'] && isset($provinces[$data['city_id']]) ? $provinces[$data['city_id']] : '';
        $data['county_name'] = $data['county_id'] && isset($provinces[$data['county_id']]) ? $provinces[$data['county_id']] : '';
        $data['area'] = $data['province_name'] . $data['city_name'];
        $this->repository->scopeQuery(function ($query) {
            return $query->where('charger_id', getUserId());
        })->update(removeNullValue($data), $id);
        $content = '';
        if ($old->cus_level != $data['cus_level']) {
            $content .= '客户等级由 ' . (isset($this->cusLevelArr[$old['cus_level']]) ? $this->cusLevelArr[$old['cus_level']] : '未定义') . '变更为'
                . (isset($this->cusLevelArr[$data['cus_level']]) ? $this->cusLevelArr[$data['cus_level']] : '未定义') . ';';
        }

        foreach ($data as $field => $value) {
            if ($value != $old->{$field} && !in_array($field, ['cus_level', 'province_id', 'city_id', 'county_id', 'area'])) {
                $content .= $transfer[$field] . '由 ' . (isset($old[$field]) ? $old[$field] : '未定义') . '变更为' . (isset($data[$field]) ? $data[$field] : '未定义') . ';';
            }
        }

        if ($content != '') {
            $log = [
                'resource_id' => $id,
                'content'     => $content
            ];
            app(CrmOperateLog::class)->batchInsert('crm_users', '编辑客户信息', [$log]);
        }
        return $this->success('修改成功');
    }

    /**
     * Mark the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function mark(Request $request, $id)
    {
        if ($id == 'batch') {
            $id = (array)$request->input('id');
        } else {
            $id = (array)$id;
        }

        $customers = $this->repository->makeModel()->newQuery()->whereIn('id', $id)
            ->where('charger_id', getUserId())->get();
        $logs = [];
        foreach ($customers as $cus) {
            $logs[] = [
                'resource_id' => $cus->id,
                'content'     => "",
                'type'        => $cus->is_mark ? "取消了标记" : "标记了客户"
            ];
        }
        DB::transaction(function () use ($id, $logs) {
            $id = implode(',', $id);
            $userId = getUserId();
            $sql = " update crm_users set is_mark = (case when is_mark =0 then 1 else 0 end) where id in ($id) "
                . " and charger_id = $userId";
            DB::statement($sql);
            app(CrmOperateLog::class)->batchInsert('crm_users', '', $logs);
        });
        return $this->success('操作成功！');
    }

    /**
     * Top the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function top(Request $request, $id)
    {
        if ($id == 'batch') {
            $id = (array)$request->input('id');
        } else {
            $id = (array)$id;
        }

        $customers = $this->repository->makeModel()->newQuery()->whereIn('id', $id)
            ->where('charger_id', getUserId())->get();
        $logs = [];
        foreach ($customers as $cus) {
            $logs[] = [
                'resource_id' => $cus->id,
                'content'     => "",
                'type'        => $cus->is_top ? "取消了置顶" : "置顶了客户"
            ];
        }
        DB::transaction(function () use ($id, $logs) {
            $id = implode(',', $id);
            $userId = getUserId();
            $sql = " update crm_users set is_top = (case when is_top =0 then 1 else 0 end) where id in ($id) "
                . " and charger_id = $userId";
            DB::statement($sql);
            app(CrmOperateLog::class)->batchInsert('crm_users', '', $logs);
        });
        return $this->success('操作成功！');
    }

    public function moveView()
    {
        $allUnderPositionIds = app(PositionStaff::class)->allUnderStaffIds(getUserId(), false, true);
        $list = Manager::query()->whereIn('id', $allUnderPositionIds)->orderBy("id", "desc")->get();
        $managers = ["" => ""];
        foreach ($list as $item) {
            $managers[$item->id] = $item->name . "($item->mobile)";
        }
        return $this->view('crm.customer.move', compact('managers'));
    }

    /**
     * 转移客户给他人操作
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function move(Request $request)
    {
        $managers = allUsersArr();
        if (($ids = $request->ids) && ($staffId = $request->staff_id)) {
            $ids = explode(',', $ids);
            $data = [
                'pre_charger_id'   => getUserId(),
                'pre_charger_name' => getUserName(),
                'charger_id'       => $staffId,
                'charger_name'     => $managers[$staffId]['name'],
                'allotted_time'    => time()
            ];
            $logs = [];
            foreach ($ids as $id) {
                $logs[] = [
                    'resource_id'    => $id,
                    'target_user_id' => $staffId
                ];
            }
            DB::transaction(function () use ($ids, $data, $logs) {
                $res = $this->repository->makeModel()->newQuery()
                    ->where('charger_id', getUserId())
                    ->whereIn('id', $ids)
                    ->update($data);
                CrmTeamList::query()->whereIn('customer_id', $ids)->delete();
                if ($res) app(CrmOperateLog::class)->batchInsert('crm_users', '转移客户给', $logs);
            });
            return $this->success('客户转移成功!');
        }
        return $this->error('操作失败!');
    }

    public function moveSeaView()
    {
        $seas = CrmOpenSea::query()->pluck('name', 'id');
        return $this->view('crm.customer.move_sea', compact('seas'));
    }

    /**
     * 转移客户入公海
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function moveSea(Request $request)
    {
        if (($ids = $request->ids) && ($seaId = $request->sea_id)) {
            $ids = explode(',', $ids);
            $data = [
                'pre_charger_id'   => getUserId(),
                'pre_charger_name' => getUserName(),
                'charger_id'       => 0,
                'charger_name'     => '',
                'sea_type'         => $seaId
            ];
            $sea = CrmOpenSea::query()->find($seaId);
            $logs = [];
            foreach ($ids as $id) {
                $logs[] = [
                    'resource_id' => $id,
                    'content'     => $sea->name
                ];
            }
            DB::transaction(function () use ($ids, $data, $logs) {
                $res = $this->repository->makeModel()->newQuery()
                    ->where('charger_id', getUserId())
                    ->whereIn('id', $ids)
                    ->update($data);
                CrmTeamList::query()->whereIn('customer_id', $ids)->delete();
                if ($res) app(CrmOperateLog::class)->batchInsert('crm_users', '移入公海', $logs);
            });
            return $this->success('客户转移成功!');
        }
        return $this->error('操作失败!');
    }

    /**
     * 查询子区域
     * @param $id
     * @return array
     */
    public function agent($id)
    {
        $data = BlAgent::query()
            ->where("pid", $id)
            ->orderBy("sort")
            ->pluck("name", "id")
            ->toArray();
        return $this->success('', $data);
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

    //模糊搜索客户
    public function selfCus(Request $request)
    {
        $customers = CrmUser::query()->where('charger_id', getUserId());
        if ($serach = $request->get('Name')) {
            $customers->where(function ($query) use ($serach) {
                $query->where('name', 'like', "%$serach%")
                    ->orWhere('mobile', 'like', "%$serach%");
            });
        }
        $customers = $customers->select('id', 'name', 'mobile')->limit(10)->get();
        $returnData = [];
        foreach ($customers as $customer) {
            $returnData[] = [
                'id'   => $customer->id,
                'name' => $customer->name ? $customer->name . "($customer->mobile)" : $customer->mobile
            ];
        }
        return $returnData;
    }

    //查询客户联系人和团队成员
    public function contactsAndTeams(Request $request)
    {
        $contacts = [];
        $teams = [];
        if ($cusId = $request->get('cusId')) {
            $list = CrmContact::query()->where('cus_id', $cusId)->get();
            foreach ($list as $cus) {
                $contacts[$cus->id] = $cus->name ?? $cus->mobile;
            }
            $teams = CrmTeamList::getCusTeamers($cusId);
        }
        $user = getUser();
        $teams[$user->id] = $user->name;
        return ['contacts' => $contacts, 'teams' => $teams];
    }
}
