<?php

namespace Modules\Manage\Http\Controllers;

use App\Models\ManageMenu;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Manage\Repositories\ManagerPermissionRepository;

class ManagerPermissionController extends Controller
{
    protected $repository;

    public function __construct(ManagerPermissionRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $result = $this->repository->get(['id', 'pid', 'level', 'name']);

        $tree = listToTree($result->toArray());

        return $this->view('manager-permission.index', compact('tree'));
    }

    public function create(Request $request, Router $router)
    {
        $pid = (int)$request->input('id');

        $parent = $pid ? $this->repository->find($pid) : null;
        $menuRoute = array_filter(ManageMenu::query()->pluck('route')->toArray());
        $routes = array_keys($router->getRoutes()->getRoutesByName());
        $routes = array_unique(array_merge($menuRoute, $routes));

        return $this->view('manager-permission.create', compact('parent', 'routes'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|min:2',
            'pid'  => 'required',
        ], [], [
            'name' => '权限名称',
            'pid'  => '上级模块'
        ]);

        $parent = $data['pid'] ? $this->repository->find($data['pid']) : null;

        $data['level'] = $parent ? $parent->level + 1 : 0;

        $this->repository->add($data, (array)$request->input('action'));

        return $this->success('权限添加成功！');

    }

    public function edit(Router $router, $id)
    {
        $model = $this->repository->find($id);

        $parent = $model->parent;

        $actions = $model->actions->pluck('action')->toJson();

        $routes = array_keys($router->getRoutes()->getRoutesByName());

        return $this->view('manager-permission.edit', compact('model', 'parent', 'routes', 'actions'));
    }

    public function update(Request $request, $id)
    {
        $data = $this->validate($request, [
            'name' => 'required|min:2',
        ], [], [
            'name' => '权限名称',
        ]);

        $this->repository->modify($id, $data, (array)$request->input('action'));

        return $this->success('权限保存成功！');
    }

    public function destroy($id)
    {
        $model = $this->repository->find($id);

        $model->actions()->delete();

        $model->delete();

        return $this->success('删除成功！');
    }
}
