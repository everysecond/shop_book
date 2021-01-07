<?php

namespace Modules\Manage\Http\Controllers;

use App\Common\Helpers\Tree;
use Illuminate\Http\Request;
use Modules\Manage\Repositories\ManagerPermissionRepository;
use Modules\Manage\Repositories\ManagerRoleRepository;

class ManagerRoleController extends Controller {
    protected $repository;

    public function __construct(ManagerRoleRepository $repository) {
        parent::__construct();

        $this->repository = $repository;
    }

    public function index(Request $request) {
        $result = $this->repository->paginate($request->input('limit'));

        $options = $this->repository->skipCriteria()->pluck('name', 'id');

        return $this->view('manager-role.index', compact('result', 'options'));
    }

    public function create() {
        return $this->view('manager-role.create', compact('options'));
    }

    public function store(Request $request) {
        $data = $this->validate($request, [
            'name' => 'required|unique:manager_roles'
        ], [], [
            '角色名',
            '显示名称'
        ]);

        $this->repository->create($data);

        return $this->success();
    }

    public function edit($id) {
        $model = $this->repository->find($id);

        return $this->view('manager-role.edit', compact('model'));
    }

    public function update(Request $request, $id) {
        $data = $this->validate($request, [
            'name' => 'required|unique:manager_roles,name,' . $id
        ], [], [
            '角色名',
            '显示名称'
        ]);

        $this->repository->update($data, $id);

        return $this->success('管理员修改成功！');
    }

    public function destroy($id) {
        $role = $this->repository->find($id);
        if ($role->isSuper()) {
            return $this->error('禁止删除超管角色！');
        }

        $this->repository->delete($id);

        return $this->success('删除管理员角色成功！');
    }

    public function permission(Request $request, ManagerPermissionRepository $repository) {
        $id = (int)$request->input('id');

        $model = $this->repository->find($id);
        $rolePermissions = $model->permissions()->pluck('id');
        $rolePermissions = array_values(array_diff($rolePermissions->toArray(),$repository->getAllParentPermissions()));

        $result = $repository->get(['id', 'pid', 'name as title']);
        foreach ($result as $item) {
            $item->spread = true;
        }
        $permissions = Tree::toChildren($result);

        return $this->view('manager-role.permission', compact('model', 'permissions', 'rolePermissions'));
    }

    public function putPermission(Request $request) {
        $id = (int)$request->input('id');
        $perIds = (array)$request->input('perIds');

        $model = $this->repository->find($id);

        if ($model->isSuper()) {
            return $this->error('此角色为超管，无需授权！');
        }

        $model->permissions()->sync($perIds);

        return $this->success('权限保存成功！');
    }
}
