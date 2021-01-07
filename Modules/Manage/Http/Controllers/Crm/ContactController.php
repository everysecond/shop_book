<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/27 17:55
 */

namespace Modules\Manage\Http\Controllers\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Crm\CrmOperateLog;
use Modules\Manage\Models\Crm\CrmTeamList;
use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\Position;
use Modules\Manage\Models\PositionStaff;
use Modules\Manage\Repositories\Crm\CrmContactRepository;
use Modules\Manage\Transformers\CrmContactResource;

class ContactController extends Controller
{
    protected $repository;

    public function __construct(CrmContactRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return $this->view('crm.contact.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $user = getUser();
        $userName = $user->name;
        $list = CrmUser::query()->where('charger_id', $user->id)->limit(10)->get();
        $customers = [];
        foreach ($list as $cus) {
            $customers[$cus->id] = $cus->name ? $cus->name : $cus->mobile;
        }
        if ($customers == []) {
            $customers = ["" => "您还没有客户"];
        }

        return $this->view('crm.contact.create', compact('customers', 'userName'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function createByConsole()
    {
        $user = getUser();
        $userName = $user->name;

        return $this->view('crm.contact.create_by_console', compact('userName'));
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
            'cus_id'   => 'required',
            'name'     => 'required',
            'mobile'   => 'required|mobile',
            'position' => 'max:100',
            'wechat'   => 'max:100',
            'address'  => 'max:255',
        ], [], [
            'cus_id'   => '客户',
            'name'     => '姓名',
            'mobile'   => '手机号',
            'position' => '职务',
            'wechat'   => '微信',
            'address'  => '具体地址',
        ]);
        $data['created_by'] = getUserId();
        $data['sex'] = $request->sex;
        $data['is_key'] = $request->is_key;
        $data['memo'] = $request->memo ?? '';
        $this->repository->create(removeNullValue($data));
        $log = [
            'resource_id' => $data['cus_id'],
            'content'     => $data['name']
        ];
        app(CrmOperateLog::class)->batchInsert('crm_users', '新增联系人', [$log]);
        return $this->success("添加成功");
    }

    /**
     * @param Request $request
     * @return \App\Http\Resources\ResourceCollection
     */
    public function paginate(Request $request)
    {
        $result = $this->repository->scopeQuery(function ($query) use ($request) {
            if ($isKey = $request->is_key) {
                $query->where('is_key', $isKey);
            }
            if ($search = $request->searchStr) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%")
                        ->orWhereHas('cus', function ($query) use ($search) {
                            $query->where('name', 'like', "%$search%")
                                ->orWhere('mobile', 'like', "%$search%");
                        });
                });
            }
            if ($date = $request->date) {
                $dateArr = explode(" - ", $date);
                $query->whereBetween('created_at', [strtotime($dateArr[0]), strtotime($dateArr[1] . ' 23:59:59')]);
            }
            $type = $request->type ?? 'myself';
            if ($type == 'myself') {//自己客户联系人
                $query->whereHas('cus', function ($query) {
                    $query->where('charger_id', getUserId());
                });
            } elseif ($type == 'under') {//下属客户联系人
                //下属职员ids
                $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
                if (!empty($underStaffIds)) {
                    $query->whereHas('cus', function ($query) use ($underStaffIds) {
                        $query->whereIn('charger_id', array_unique($underStaffIds));
                    });
                } else {
                    $query->where("id", "<", 0);
                }
            } elseif ($type == 'myteam') {
                $query->whereHas('cus', function ($query) {
                    $teamCus = CrmTeamList::query()->where([
                        'user_id'   => getUserId(),
                        'team_role' => CrmTeamList::ROLE_TWO
                    ])->pluck('customer_id')->toArray();
                    $query->whereIn('id', array_unique($teamCus))->where('charger_id', '!=', getUserId());
                });
            } elseif ($type == 'underteam') {
                $query->whereHas('cus', function ($query) {
                    //下属职员ids
                    $underStaffIds = array_unique(app(PositionStaff::class)->allUnderStaffIds(getUserId()));
                    if (!empty($underStaffIds)) {
                        $teamCus = CrmTeamList::query()->where('team_role', CrmTeamList::ROLE_TWO)
                            ->whereIn('user_id', $underStaffIds)->pluck('customer_id')->toArray();
                        $query->whereIn('id', array_unique($teamCus));
                    } else {
                        $query->where("id", "<", 0);
                    }
                });
            }
            return $query;
        })->with(['createdUser', 'cus'])->paginate(request('limit'));
        return CrmContactResource::collection($result);
    }

    /**
     * @param Request $request
     * @return \App\Http\Resources\ResourceCollection
     */
    public function paginateByCus($id)
    {
        $result = $this->repository->with(['createdUser'])->findByField('cus_id', $id);
        return CrmContactResource::collection($result);
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
        $model = $this->repository->with(['createdUser', 'cus'])->find($id);
        return $this->view('crm.contact.edit', compact('model', 'request'));
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
        $data = $this->validate($request, [
            'name'     => 'required',
            'mobile'   => 'required|mobile',
            'position' => 'max:100',
            'wechat'   => 'max:100',
            'address'  => 'max:255',
        ], [], [
            'name'     => '姓名',
            'mobile'   => '手机号',
            'position' => '职务',
            'wechat'   => '微信',
            'address'  => '具体地址',
        ]);
        $data['sex'] = $request->sex;
        $data['is_key'] = $request->is_key;
        $data['memo'] = $request->memo ?? '';
        $this->repository->whereHas('cus', function ($query) {
            $query->where('charger_id', getUserId());
        })->update(removeNullValue($data), $id);
        return $this->success('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        if ($id == 'batch') {
            $id = (array)$request->input('id');
        } else {
            $id = (array)$id;
        }

        $contacts = $this->repository->makeModel()->newQuery()->whereIn('id', $id)
            ->whereHas('cus', function ($query) {
                $query->where('charger_id', getUserId());
            })->get();
        $logs = [];
        foreach ($contacts as $contact) {
            $logs[] = [
                'resource_id' => $contact->cus_id,
                'content'     => $contact->name . "($contact->mobile)"
            ];
        }
        DB::transaction(function () use ($id, $logs) {
            $this->repository->makeModel()->newQuery()->whereIn('id', $id)
                ->whereHas('cus', function ($query) {
                    $query->where('charger_id', getUserId());
                })->delete();
            app(CrmOperateLog::class)->batchInsert('crm_users', '删除联系人', $logs);
        });
        return $this->success('删除联系人成功！');
    }
}
