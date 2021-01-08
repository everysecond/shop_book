<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/15 13:41
 */

namespace Modules\Manage\Http\Controllers\Crm;

use App\Common\Helpers\Tree;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Position;
use Modules\Manage\Models\PositionStaff;
use Modules\Manage\Repositories\System\PositionRepository;

class PositionController extends Controller
{
    protected $repository;

    public function __construct(PositionRepository $repository)
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
        $defaultPosition = $this->repository->first()??new Position();
        return $this->view('crm.position.index',compact("defaultPosition"));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $list = $this->repository->skipCriteria()->get();
        $positions = (new Tree($list))->getSelect(null, ["value" => "title"]);
        $positions->prepend('', '');
        $options = [];
        foreach ($positions as $k => &$option) {
            $option = str_replace(["├─","&nbsp;","└─"], "", $option);
            $options[$k] = $option;
        }

        return $this->view('crm.position.create', compact('options'));
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
            'title' => 'required|min:2|unique:positions',
            'sort' => 'int',
        ], [], [
            'title' => '职位名称'
        ]);
        if($pid = $request->pid){
            $data["pid"] = $pid;
            $parent = $this->repository->find($pid);
            $data["level"] = $parent?$parent->level+1:0;
        }
        $this->repository->create(removeNullValue($data));
        return $this->success("添加成功");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function show()
    {
        $data = Position::query()->where("pid", 0)
            ->with('children')->orderBy("sort","desc")->get()->toArray();
        return $this->success('', $this->formateList($data));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $model = $this->repository->find($id);
        $list = $this->repository->skipCriteria()->get();
        $positions = (new Tree($list))->getSelect(null, ["value" => "title"]);
        $positions->prepend('', '');
        $options = [];
        foreach ($positions as $k => &$option) {
            $option = str_replace("&nbsp;", "", $option);
            $option = str_replace("└─", "", $option);
            $option = str_replace("├─", "", $option);
            $options[$k] = $option;
        }
        return $this->view('crm.position.edit', compact("model", "options"));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $this->validate($request, [
            'title' => 'required|unique:positions,title,' . $id,
            'sort' => 'int',
        ], [], [
            'title' => '职位名称',
        ]);

        if($pid = $request->pid){
            $data["pid"] = $pid;
            $parent = $this->repository->find($pid);
            $data["level"] = $parent?$parent->level+1:0;
        }else{
            $data["pid"] = 0;
            $data["level"] = 0;
        }
        $this->repository->update(removeNullValue($data), $id);
        return $this->success('职位修改成功');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->repository->makeModel()->where('id', $id)->delete();
        PositionStaff::query()->where("position_id",$id)->delete();
        return $this->success('删除职位成功！');
    }

    public function formateList($data)
    {
        foreach ($data as &$datum) {
            $datum["spread"] = true;
            if (!empty($datum["children"])) {
                $datum["children"] = $this->formateList($datum["children"]);
            }
        }
        return $data;
    }
}
