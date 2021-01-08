<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/21 16:24
 */

namespace Modules\Manage\Http\Controllers\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Crm\CrmOpenSea;
use Modules\Manage\Models\Crm\CrmRuleSetting;
use Modules\Manage\Models\Crm\CrmSeaStaff;

class SeaController extends Controller
{
    protected $model;

    public function __construct(CrmOpenSea $openSea)
    {
        parent::__construct();
        $this->model = $openSea;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $defaultPosition = $this->model->newQuery()->first() ?? new CrmOpenSea();
        return $this->view('crm.sea.index', compact("defaultPosition"));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return $this->view('crm.sea.create');
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
            'name' => 'required|min:2|unique:crm_open_seas'
        ], [], [
            'name' => '公海名称'
        ]);
        $this->model->newQuery()->create(removeNullValue($data));
        return $this->success("添加成功");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function show()
    {
        return $this->success('', CrmOpenSea::query()->get()->toArray());
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $model = $this->model->find($id);
        return $this->view('crm.sea.edit', compact("model"));
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
            'name' => 'required|min:2|unique:crm_open_seas,name,' . $id
        ], [], [
            'name' => '公海名称',
        ]);
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
        $check = true;
        $rules = CrmRuleSetting::query()->get();
        foreach ($rules as $rule) {
            $json = json_decode($rule->json, true);
            if (isset($json["international_waters_1"]) && $json["international_waters_1"] == $id) {
                $check = false;
            }
            if (isset($json["international_waters_2"]) && $json["international_waters_2"] == $id) {
                $check = false;
            }
        }
        if($check){
            $this->model->where('id', $id)->delete();
            CrmSeaStaff::query()->where("sea_id", $id)->delete();
            return $this->success('删除成功！');
        }else{
            return $this->error("该公海已含有客户或已被公海规则使用,无法删除!");
        }
    }
}
