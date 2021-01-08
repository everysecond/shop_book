<?php

namespace Modules\Manage\Http\Controllers;

use App\Common\Helpers\Tree;
use App\Models\ManageMenu;
use Illuminate\Http\Request;
use Modules\Manage\Repositories\ManageMenuRepository;

class ManageMenuController extends Controller
{
    protected $repository;

    public function __construct(ManageMenuRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        return $this->view('manage-menu.index');
    }

    public function menus()
    {
        return $this->repository->orderBy('sort')->get()->toArray();
    }

    public function create()
    {
        $list = $this->repository->skipCriteria()->get();
        $options = (new Tree($list))->getSelect();
        $options->prepend('根菜单', 0);

        return $this->view('manage-menu.create', compact('options'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name'     => 'required',
            'pid'      => 'nullable',
            'route'    => 'nullable',
            'icon'     => 'nullable',
            'terminal' => 'nullable',
            'target'   => 'nullable',
        ], [], [
            'name'     => '菜单名称',
            'terminal' => '归属终端',
            'target'   => '窗口打开位置',
        ]);

        $this->repository->create(removeNullValue($data));

        return $this->success('添加菜单成功！');
    }

    public function edit($id)
    {
        $model = $this->repository->find($id);

        $list = $this->repository->skipCriteria()->get();
        $options = (new Tree($list))->getSelect();
        $options->prepend('根菜单', 0);

        return $this->view('manage-menu.edit', compact('model', 'options'));
    }

    public function update(Request $request, $id)
    {
        $data = $this->validate($request, [
            'name'     => 'required',
            'pid'      => 'nullable',
            'route'    => 'nullable',
            'icon'     => 'nullable',
            'terminal' => 'nullable',
            'target'   => 'nullable',
        ], [], [
            'name'     => '菜单名称',
            'terminal' => '归属终端',
            'target'   => '窗口打开位置',
        ]);

        $this->repository->update(removeNullValue($data), $id);

        return $this->success('编辑菜单成功！！');
    }

    public function destroy($id)
    {
        $this->repository->delete($id);

        return $this->success('删除菜单成功！');
    }

    public function icon()
    {
        return $this->view('manage-menu.icon');
    }

    public function change(Request $request)
    {
        $data = $this->validate($request, [
            'id'    => 'required',
            'name'  => 'required',
            'value' => 'nullable'
        ], [], [
            '菜单名称',
        ]);

        $model = $this->repository->find($data['id']);

        if (!in_array($data['name'], ['sort'])) {
            return $this->error('非法操作！');
        }

        if ($data['name'] == 'sort') {
            $data['value'] = intval($data['value'] ?? 0);
        }

        $model->update([
            $data['name'] => $data['value']
        ]);

        return $this->success('保存成功！');
    }

    public function changeStatus(Request $request)
    {
        $data = $this->validate($request, [
            'id'    => 'required',
            'name'  => 'required',
            'value' => 'nullable'
        ], [], [
            '菜单名称',
        ]);

        $model = $this->repository->find($data['id']);

        if (!in_array($data['name'], ['status'])) {
            return $this->error('非法操作！');
        }

        if ($data['name'] == 'status') {
            $data['value'] = $data['value'] == "true" ? 1 : 0;
        }

        $model->update([
            $data['name'] => $data['value']
        ]);

        return $this->success('保存成功！');
    }
}
