<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/9/6 16:49
 */

namespace Modules\Manage\Http\Controllers\Crm;

use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Crm\CrmContact;
use Modules\Manage\Models\Crm\CrmImage;
use Modules\Manage\Models\Crm\CrmOperateLog;
use Modules\Manage\Models\Crm\CrmPlanRecord;
use Modules\Manage\Models\Crm\CrmTeamList;
use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\Position;
use Modules\Manage\Models\PositionStaff;
use Modules\Manage\Repositories\Crm\CrmPlanRecordRepository;
use Modules\Manage\Transformers\CrmContactResource;
use Modules\Manage\Transformers\CrmPlanRecordResource;

class PlanController extends Controller
{
    protected $repository;

    public function __construct(CrmPlanRecordRepository $repository)
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
        return $this->view('crm.follow.index');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function planIndex()
    {
        return $this->view('crm.follow.plan_index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        $user = getUser();
        $contacts = [];
        $teams = [];
        if ($cusId = $request->get('cusId')) {
            $list = CrmContact::query()->where('cus_id', $cusId)->get();
            foreach ($list as $cus) {
                $contacts[$cus->id] = $cus->name ?? $cus->mobile;
            }
            $teams = CrmTeamList::getCusTeamers($cusId);
        }
        $teams[$user->id] = $user->name;
        $list = Manager::query()->orderBy("id", "desc")->get();
        $managers = ["" => ""];
        foreach ($list as $item) {
            $managers[$item->id] = $item->name;
        }

        $view = $request->get('type', 1) == 1 ? 'create' : 'create_plan';
        return $this->view('crm.follow.' . $view, compact('contacts', 'user', 'managers', 'teams'));
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
            'type'            => 'required|int',
            'cus_id'          => 'required|int',
            'follow_mode'     => 'required|int',
            'contact_id'      => '',
            'follow_at'       => 'required',
            'follow_user_ids' => 'required',
            'content'         => 'required',
        ], [], [
            'type'            => '记录类型',
            'cus_id'          => '客户',
            'follow_mode'     => '跟进方式',
            'contact_id'      => '联系人',
            'follow_at'       => '跟进时间',
            'follow_user_ids' => '跟进人员',
            'content'         => '跟进内容',
        ]);
        $data['created_by'] = getUserId();
        $data['follow_at'] = strtotime($data['follow_at']);

        DB::transaction(function () use ($data, $request) {
            $follow = $this->repository->create(removeNullValue($data));
            $log = [
                'resource_id' => $data['cus_id']
            ];
            $type = $data['type'] == 1 ? '添加跟进记录' : '添加跟进计划';
            app(CrmOperateLog::class)->batchInsert('crm_users', $type, [$log]);

            if ($imgIds = $request->img_ids) {
                CrmImage::query()->whereIn('id', $imgIds)->update(['resource_id' => $follow->id]);
            }
        });

        return $this->success("添加成功");
    }

    /**
     * @param Request $request
     * @return \App\Http\Resources\ResourceCollection
     */
    public function paginate(Request $request)
    {
        $result = $this->repository->scopeQuery(function ($query) use ($request) {
            if ($mode = $request->follow_mode) {
                $query->where('follow_mode', $mode);
            }

            if ($recordType = $request->record_type) $query->where('type', $recordType);

            if ($search = $request->searchStr) {
                $query->whereHas('cus', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                        ->orWhere('owner_name', 'like', "%$search%")
                        ->orWhere('short_name', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%");
                });
            }
            if ($date = $request->date) {
                $dateArr = explode(" - ", $date);
                $query->whereBetween('follow_at', [strtotime($dateArr[0]), strtotime($dateArr[1] . ' 23:59:59')]);
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
        })->with(['createdUser', 'cus', 'contact', 'images'])->paginate(request('limit'));
        return CrmPlanRecordResource::collection($result);
    }

    public function plans(Request $request)
    {
        try {
            $result = $this->repository->scopeQuery(function ($query) use ($request) {
                if ($mode = $request->follow_mode) {
                    $query->where('follow_mode', $mode);
                }

                if ($recordType = $request->record_type) $query->where('type', $recordType);

                if ($search = $request->searchStr) {
                    $query->whereHas('cus', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%")
                            ->orWhere('owner_name', 'like', "%$search%")
                            ->orWhere('short_name', 'like', "%$search%")
                            ->orWhere('mobile', 'like', "%$search%");
                    });
                }
                if ($date = $request->date) {
                    $dateArr = explode(" - ", $date);
                    $query->whereBetween('follow_at', [strtotime($dateArr[0]), strtotime($dateArr[1] . ' 23:59:59')]);
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
                } elseif ($type == 'myall') {
                    //下属职员ids
                    $underStaffIds = app(PositionStaff::class)->allUnderStaffIds(getUserId());
                    //带上当前登录人
                    $underStaffIdsp[] = getUserId();
                    $query->whereHas('cus', function ($query) use ($underStaffIds) {
                        $query->whereIn('charger_id', array_unique($underStaffIds));
                    });
                } else {//具体某个下属的跟进计划
                    $query->whereHas('cus', function ($query) use ($type) {
                        $query->where('charger_id', $type);
                    });
                }
                return $query;
            })->with(['createdUser', 'cus', 'contact', 'images'])->orderBy('follow_at', 'desc')
                ->paginate($request->get('limit', 6));
            return retArr('', ['data' => CrmPlanRecordResource::collection($result)]);
        } catch (\Exception $exception) {
            Log::error('控制台跟进计划错误：' . $exception);
            return retArr('系统异常', [], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $model = $this->repository->with(['cus'])->find($id);
        $model->follow_users = explode(',', $model->follow_user_ids);

        $contacts = [];
        $list = CrmContact::query()->where('cus_id', $model->cus_id)->get();
        foreach ($list as $cus) {
            $contacts[$cus->id] = $cus->name ?? $cus->mobile;
        }
        $teams = CrmTeamList::getCusTeamers($model->cus_id);
        $teams[getUserId()] = getUserName();

        $list = allUsersArr();
        $managers = ["" => ""];
        foreach ($list as $item) {
            $managers[$item['id']] = $item['name'];
        }
        return $this->view('crm.follow.edit', compact('model', 'contacts', 'managers', 'teams'));
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
            'type'            => 'required|int',
            'cus_id'          => 'required|int',
            'follow_mode'     => 'required|int',
            'contact_id'      => '',
            'follow_at'       => 'required',
            'follow_user_ids' => 'required',
            'content'         => 'required',
        ], [], [
            'type'            => '记录类型',
            'cus_id'          => '客户',
            'follow_mode'     => '跟进方式',
            'contact_id'      => '联系人',
            'follow_at'       => '跟进时间',
            'follow_user_ids' => '跟进人员',
            'content'         => '跟进内容',
        ]);
        $data['follow_at'] = strtotime($data['follow_at']);


        DB::transaction(function () use ($data, $request, $id) {
            $this->repository->whereHas('cus', function ($query) {
                $query->where('charger_id', getUserId());
            })->update(removeNullValue($data), $id);
            $log = [
                'resource_id' => $data['cus_id']
            ];
            app(CrmOperateLog::class)->batchInsert('crm_users', '跟进计划转记录', [$log]);

            if ($imgIds = $request->img_ids) {
                CrmImage::query()->whereIn('id', $imgIds)->update(['resource_id' => $id]);
            }
        });

        return $this->success("转为记录成功!");
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        ;
        if ($id == 'batch') {
            $id = (array)$request->input('id');
        } else {
            $id = (array)$id;
        }

        $records = $this->repository->makeModel()->newQuery()->whereIn('id', $id)
            ->whereHas('cus', function ($query) {
                $query->where('charger_id', getUserId());
            })->get();
        $logs = [];
        foreach ($records as $record) {
            $logs[] = [
                'resource_id' => $record->cus_id
            ];
        }
        DB::transaction(function () use ($id, $logs) {
            $this->repository->makeModel()->newQuery()->whereIn('id', $id)
                ->whereHas('cus', function ($query) {
                    $query->where('charger_id', getUserId());
                })->delete();
            app(CrmOperateLog::class)->batchInsert('crm_users', '取消跟进计划', $logs);
        });
        return $this->success('取消跟进计划成功！');
    }
}
