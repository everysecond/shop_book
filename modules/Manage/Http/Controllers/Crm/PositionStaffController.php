<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/19 19:49
 */

namespace Modules\Manage\Http\Controllers\Crm;

use App\Common\Helpers\Tree;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Position;
use Modules\Manage\Repositories\System\PositionStaffRepository;
use Modules\Manage\Transformers\PositionStaffResource;

class PositionStaffController extends Controller
{
    protected $repository;

    public function __construct(PositionStaffRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        $hasIds = $this->repository->findByField("position_id", $request->position_id)->pluck("staff_id")->toArray();
        $list = Manager::query()->whereNotIn("id", $hasIds)->orderBy("id", "desc")->get();
        $options = ["" => ""];
        foreach ($list as $item) {
            $options[$item->id] = $item->name . "($item->mobile)";
        }

        $list = Position::get();
        $list = (new Tree($list))->getSelect(null, ["value" => "title"]);
        $list->prepend('', '');
        $positions = [];
        foreach ($list as $k => &$option) {
            $option = str_replace(["├─", "&nbsp;", "└─"], "", $option);
            $positions[$k] = $option;
        }
        return $this->view('crm.position-staff.create', compact('options', 'positions'));
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
            'position_id' => $request->position_id,
            'staff_id' => $request->staff_id
        ];
        $data = $this->validate($request, [
            'position_id' => 'required',
            'staff_id'    => ['required',Rule::unique("position_staff")->where(function ($query)use($where) {
                return $query->where($where);
            })],
        ], [], [
            'position_id' => '职位',
            'staff_id'    => '人员'
        ]);
        $data["see_level"] = $request->see_level ?? 0;
        $this->repository->create(removeNullValue($data));
        return $this->success("添加成功");
    }

    public function paginate()
    {
        $result = $this->repository->makeModel()->with('staff')
            ->where(["position_id"=>request('position_id')])
            ->paginate(request('limit'));

        return PositionStaffResource::collection($result);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->repository->makeModel()->where('id', $id)->delete();
        return $this->success('移除成功！');
    }
}
