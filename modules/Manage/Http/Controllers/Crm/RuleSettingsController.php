<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/8 11:36
 */

namespace Modules\Manage\Http\Controllers\Crm;

use \Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Models\Crm\CrmOpenSea;
use Modules\Manage\Models\Crm\CrmRuleSetting;
use Modules\Manage\Models\Crm\OpenSea;
use Modules\Manage\Models\Crm\RuleSetting;
use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Service\LeaseIncomeLog;
use Modules\Manage\Models\Service\LeaseServiceWithdraw;
use Modules\Manage\Models\Service\LeaseServiceWithdrawLog;
use Modules\Manage\Models\Service\LeaseServiceWithdrawRate;
use Modules\Manage\Repositories\Report\LeaseServiceRepository;
use Modules\Manage\Services\ChartService;

class RuleSettingsController extends Controller
{
    protected $repository;
    protected $chartService;
    protected $incomeModel;
    protected $leaseServiceWithdraw;

    public function __construct(LeaseServiceRepository $repository, ChartService $chartService,LeaseIncomeLog $log, LeaseServiceWithdraw $leaseServiceWithdraw)
    {
        $this->repository = $repository;
        $this->chartService = $chartService;
        $this->incomeModel = $log;
        $this->leaseServiceWithdraw = $leaseServiceWithdraw;
    }


    public function index()
    {
        $data = app(CrmRuleSetting::Class)->pluck('json','type')->toArray();
        $data_1 =  $data_2 = [];

        if (!empty($data[1])) $data_1 = json_decode($data[1],true);
        if (!empty($data[2])) $data_2 = json_decode($data[2],true);
        $water = app(CrmOpenSea::Class)->pluck('name','id')->toArray();


         return $this->view("lease.crm.rule_settings",[
             'data_1'=>$data_1,
             'data_2'=>$data_2,
             'water'=>$water
        ]);
    }


    public function ruleCreate(Request $request)
    {
        try {
            $req = $request->all();

            $type = $req['type'];
            $json =  json_encode($req);
            $where = ['type'=>$type];
            $data = ['type'=>$type,'json'=>$json];
            app(CrmRuleSetting::Class)->updateOrInsert($where,$data);

            return result("", 1, '保存成功');
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }


}
