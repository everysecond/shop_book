<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/27 17:55
 */

namespace Modules\Manage\Http\Controllers\Crm;

use Illuminate\Http\Request;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Crm\SysDictionary;
use Modules\Manage\Transformers\SysDictionaryResource;

class DictController extends Controller
{
    protected $model;

    public function __construct(SysDictionary $dictionary)
    {
        $this->model = $dictionary;
    }

    public function index()
    {
        return $this->view('crm.dict.index');
    }

    public function create()
    {
        return $this->view('crm.dict.create');
    }

    public function edit($id)
    {
        $model = $this->model->newQuery()->find($id);
        return $this->view('crm.dict.edit', compact('model'));
    }

    /**
     * 新增 ：字典类型及code联合唯一
     * @param Request $request
     * @throws \App\Exceptions\SystemException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            "dict_type"  => "required|string",
            "code"       => "required|string|unique:sys_dictionaries,code,,,dict_type," . $request->dict_type,
            "type_means" => "required|string",
            "means"      => "required|string"
        ], [], [
            "dict_type"  => "字典类型",
            "type_means" => "类型名称",
            "code"       => "键",
            "means"      => "值"
        ]);

        $all = $request->all();
        $data = [
            "created_by" => getUserId()
        ];
        $all['memo'] = $all['memo'] ?? "";
        $this->model->create(array_merge($data, $all));
        return $this->success();
    }

    /**
     * @param Request $request
     * @return \App\Http\Resources\ResourceCollection
     */
    public function paginate(Request $request)
    {
        $data = $this->model->newQuery()->with('createdUser')
            ->getList($request->get('page_size', 10), $request);
        return SysDictionaryResource::collection($data);
    }

    /**
     * 编辑字典 ：字典类型及code联合唯一
     * @param Request $request
     * @param $id ：字典ID
     * @throws \App\Exceptions\SystemException
     */
    public function update(Request $request, $id)
    {
        $data = $this->validate($request, [
            "dict_type"  => "required|string",
            "code"       => "required|string|unique:sys_dictionaries,code,$id,id,dict_type," . $request->dict_type,
            "type_means" => "required|string",
            "means"      => "required|string",
            "sort"       => "int",
            "memo"       => "",
        ], [], [
            "dict_type"  => "字典类型",
            "type_means" => "类型名称",
            "code"       => "键",
            "means"      => "值",
            "sort"       => "排序"
        ]);

        $data['memo'] = $data['memo'] ?? '';
        $this->model->newQuery()->whereId($id)->update($data);
        return $this->success("修改成功!");
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $this->model->whereId($id)->delete();
        return $this->success('移除成功！');
    }

    public function config(Request $request)
    {
        $data = $request->all();
        return modifyEnv($data);
    }
}
