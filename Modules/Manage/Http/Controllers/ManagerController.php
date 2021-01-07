<?php

namespace Modules\Manage\Http\Controllers;

use App\Common\Helpers\Tree;
use App\Models\ManagerRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Kood\Models\Site;
use Modules\Manage\Models\Crm\CrmSeaStaff;
use Modules\Manage\Models\Crm\CrmTeamList;
use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\PositionStaff;
use Modules\Manage\Repositories\ManagerPermissionRepository;
use Modules\Manage\Repositories\ManagerRepository;
use Modules\Manage\Repositories\ManagerRoleRepository;
use Modules\Manage\Transformers\ManagerResource;

class ManagerController extends Controller
{
    protected $repository;

    public function __construct(ManagerRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    public function index(ManagerRoleRepository $repository)
    {
        $roles = $repository->pluck('name', 'id');

        return $this->view('manager.index', compact('roles'));
    }

    public function paginate()
    {

        $result = $this->repository->with('roles', 'last_login_at')->paginate(request('limit'));

        return ManagerResource::collection($result);
    }

    public function create(ManagerRoleRepository $repository)
    {
        $roles = $repository->pluck('name', 'id');
        $agents = allLeaseProvinces();
        $agents[0] = '集团';
        ksort($agents);

        $sites = Site::query()->pluck('name', 'id')->toArray();
        $sites[0] = '集团';
        ksort($sites);

        return $this->view('manager.create', compact('roles', 'agents', 'sites'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'password' => 'required|min:5',
            'name'     => 'required',
            'mobile'   => [
                'required',
                'mobile',
                Rule::unique('managers', 'mobile')->whereNull('deleted_at')
            ],
            'agent_id' => 'required|int',
            'site_id'  => 'required|int',
        ], [], [
            'password' => '密码',
            'name'     => '姓名',
            'mobile'   => '登录手机号',
            'agent_id' => '管理区域',
            'site_id'  => '管理区域',
        ]);

        $data['password'] = $this->repository->makePassword($data['password']);
        $data['status'] = $request->input('status') ? 1 : 0;
        $model = $this->repository->create(removeNullValue($data));

        $roles = (array)$request->input('roles');
        $model->roles()->sync($roles);

        return $this->success();
    }

    public function edit(ManagerRoleRepository $repository, $id)
    {
        $model = $this->repository->find($id);

        $roles = $repository->pluck('name', 'id');
        $agents = allLeaseProvinces();
        $agents[0] = '集团';
        ksort($agents);

        $sites = Site::query()->pluck('name', 'id')->toArray();
        $sites[0] = '集团';
        ksort($sites);

        return $this->view('manager.edit', compact('model', 'roles', 'agents', 'sites'));
    }

    public function update(Request $request, $id)
    {
        $data = $this->validate($request, [
            'password' => 'nullable|min:5',
            'name'     => 'required',
            'mobile'   => [
                'required',
                'mobile',
                Rule::unique('managers')->whereNull('deleted_at')->ignore($id)
            ],
            'agent_id' => 'required|int',
            'site_id'  => 'required|int',
        ], [], [
            'password' => '密码',
            'name'     => '姓名',
            'mobile'   => '登录手机号',
            'site_id'  => '管理区域',
        ]);

        $data['password'] = $this->repository->makePassword($data['password']);
        $data['status'] = $request->input('status') ? 1 : 0;


        $model = $this->repository->update(removeNullValue($data), $id);

        $roles = (array)$request->input('roles');
        $model->roles()->sync($roles);

        return $this->success('管理员修改成功');
    }

    public function destroy(Request $request, $id)
    {
        if ($id == 'batch') {
            $id = (array)$request->input('id');
        } else {
            $id = (array)$id;
        }

        DB::transaction(function () use ($id) {
            $this->repository->makeModel()->whereIn('id', $id)->delete();
            PositionStaff::query()->whereIn('staff_id', $id)->delete();
            CrmSeaStaff::query()->whereIn('staff_id', $id)->delete();
            CrmTeamList::query()->whereIn('user_id', $id)->delete();
            CrmUser::query()->whereIn('charger_id', $id)->update([
                'charger_id'    => 0,
                'charger_name'  => '',
                'allotted_time' => 0,
                'inflow_time'   => 0
            ]);
        });

        //todo 删除角色

        //todo 删除权限

        return $this->success('删除管理员成功！');
    }

    public function permission(Request $request, ManagerPermissionRepository $repository)
    {
        $id = (int)$request->input('id');

        $model = $this->repository->find($id);

        $result = $repository->get(['id', 'pid', 'name as title']);

        $rolesPermissions = $this->repository->getRolesPermission($model);
        $managerPermissions = $model->permissions()->pluck('id')->toArray();
        $allPermissions = array_unique(array_merge($rolesPermissions, $managerPermissions));
        $allPermissions = array_values(array_diff($allPermissions, $repository->getAllParentPermissions()));
        foreach ($result as $item) {
            $item->spread = true;
        }
        $permissions = Tree::toChildren($result);

        return $this->view('manager.permission', compact('model', 'permissions', 'allPermissions'));
    }

    public function putPermission(Request $request)
    {
        $id = (int)$request->input('id');
        $perIds = (array)$request->input('perIds');

        $model = $this->repository->find($id);
        if ($model->isSuper()) {
            return $this->error('此用户为超管，无需授权！');
        }

        $rolesPermissions = $this->repository->getRolesPermission($model);
        $permissions = array_diff($perIds, $rolesPermissions);

        $model->permissions()->sync($permissions);

        return $this->success('管理员授权成功！');
    }
}
