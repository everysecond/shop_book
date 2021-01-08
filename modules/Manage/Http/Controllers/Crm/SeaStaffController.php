<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/21 16:24
 */

namespace Modules\Manage\Http\Controllers\Crm;

use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Crm\CrmOpenSea;
use Modules\Manage\Models\Crm\CrmSeaStaff;
use Modules\Manage\Transformers\CrmSeaStaffResource;

class SeaStaffController extends Controller
{
    protected $model;

    public function __construct(CrmSeaStaff $crmSeaStaff)
    {
        parent::__construct();
        $this->model = $crmSeaStaff;
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        $hasIds = $this->model->where("sea_id", $request->sea_id)->pluck("staff_id")->toArray();
        $list = Manager::query()->whereNotIn("id", $hasIds)->orderBy("id", "desc")->get();
        $options = ["" => ""];
        foreach ($list as $item) {
            $options[$item->id] = $item->name . "($item->mobile)";
        }

        $positions = CrmOpenSea::query()->pluck("name", "id");
        $positions->prepend('', '');
        return $this->view('crm.sea.staff_create', compact('options', 'positions'));
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(Request $request)
    {
        $where = [
            'sea_id'   => $request->sea_id,
            'staff_id' => $request->staff_id
        ];
        $data = $this->validate($request, [
            'sea_id'   => 'required',
            'staff_id' => ['required', Rule::unique("crm_sea_staff")->where(function ($query) use ($where) {
                return $query->where($where);
            })],
        ], [], [
            'sea_id'   => '公海',
            'staff_id' => '人员'
        ]);
        $data["can_get"] = $request->can_get ? 2 : 1;
        $data["can_assign"] = $request->can_assign ? 2 : 1;
        $this->model->create(removeNullValue($data));
        return $this->success("添加成功");
    }

    public function paginate()
    {
        $result = $this->model->with('staff')
            ->where(["sea_id" => request('position_id')])
            ->paginate(request('limit'));

        return CrmSeaStaffResource::collection($result);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $model = $this->model->find($id);
        $list = Manager::query()->orderBy("id", "desc")->get();
        $options = ["" => ""];
        foreach ($list as $item) {
            $options[$item->id] = $item->name . "($item->mobile)";
        }

        $positions = CrmOpenSea::query()->pluck("name", "id");
        $positions->prepend('', '');
        return $this->view('crm.sea.staff_edit', compact('model', 'options', 'positions'));
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
            'sea_id'   => 'required',
            'staff_id' => 'required'
        ], [], [
            'sea_id'   => '公海',
            'staff_id' => '人员'
        ]);
        $data["can_get"] = $request->can_get ? 2 : 1;
        $data["can_assign"] = $request->can_assign ? 2 : 1;
        $this->model->whereId($id)->update(removeNullValue($data));
        return $this->success('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->model->where('id', $id)->delete();
        return $this->success('移除成功！');
    }
}
