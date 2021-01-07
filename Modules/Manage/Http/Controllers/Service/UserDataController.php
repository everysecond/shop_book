<?php

namespace Modules\Manage\Http\Controllers\Service;

use \Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Lease\Models\BlService;
use Modules\Lease\Models\BlUser;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Repositories\Report\LeaseServiceRepository;
use Modules\Manage\Services\ChartService;

class UserDataController extends Controller
{
    protected $repository;
    protected $chartService;
    protected $ageArr = ["30岁以下", "31-40岁", "41-50岁", "50岁以上"];
    protected $sexArr = ["男", "女"];
    protected $hourArr = ["0:00", "1:00", "2:00", "3:00", "4:00", "5:00", "6:00", "7:00", "8:00",
        "9:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00",
        "19:00", "20:00", "21:00", "22:00", "23:00"];

    public function __construct(LeaseServiceRepository $repository, ChartService $chartService)
    {
        $this->repository = $repository;
        $this->chartService = $chartService;
    }

    //用户画像view
    public function portrayal()
    {
        $provinces = allUserProvinces();
        return view("manage::lease.service.user.portrayal", compact("provinces"));
    }

    //注册审核view
    public function register()
    {
        $provinces = allUserProvinces();
        $timeType = dateType();
        return view("manage::lease.service.user.register_chart", compact("provinces", "timeType"));
    }

    //用户年龄分布数据
    public function age(Request $request)
    {
        try {
            $ageData = $this->repository->getAgeProfile($request->agentId);
            return result("", 1, $this->chartService->pieChartFormat($ageData, $this->ageArr));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception);
            return result($exception->getMessage(), -1);
        }
    }

    //用户性别分布数据
    public function sex(Request $request)
    {
        try {
            $sexData = $this->repository->getSexProfile($request->agentId);
            $sexDataFormat = [];
            foreach ($this->sexArr as $item) {
                if ($item == "男") {
                    $value = isset($sexData[1]) ? $sexData[1] : 0;
                } else {
                    $value = isset($sexData[2]) ? $sexData[2] : 0;
                }
                $sexDataFormat[] = [
                    "value" => $value,
                    "name"  => $item
                ];
            }
            $return = [
                "sexArr"  => $this->sexArr,
                "sexData" => $sexDataFormat
            ];
            return result("", 1, $return);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception);
            return result($exception->getMessage(), -1);
        }
    }

    //用户地区分布数据
    public function area()
    {
        try {
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $provincesArr = getAllProvincesName();
            $areaData = $this->repository->getAreaProfile();
            return result("", 1, $this->chartService->mapChartFormat($areaData, $agentsMap, $provincesArr));
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //网点数排行
    public function sort(Request $request)
    {
        try {
            $data = $this->repository->sort();
            $xAxis = [];
            $allProvinceArr = allLeaseProvinces();
            foreach ($data as $provinceId => $datum) {
                if (isset($allProvinceArr[$provinceId])) {
                    $xAxis[] = $allProvinceArr[$provinceId];
                } else {
                    unset($data[$provinceId]);
                }
            }
            return result("", 1, ["xAxis" => $xAxis, "seriesData" => array_values($data)]);
        } catch (\Exception $exception) {
            Log::error("终端下载统计报错:{$exception->getMessage()}");
            return result($exception->getMessage(), -1);
        }
    }

    //注册审核趋势
    public function registerTrend(Request $request)
    {
        try {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 day")),
                "end"   => date("Y-m-d")
            ];
            $data = $this->repository->registerTrend($request, $defaultDay);
            $actuallyBegin = isset($data[0]) ? $data[0]["date"] : $defaultDay["begin"];
            $dateRange = getDateRange($actuallyBegin, $defaultDay["end"]);
            $legend = ["apply_num" => "网点申请数", "crm_num" => "网点录入数", "audited_num" => "网点审核数"];
            return result("", 1, $this->chartService->lineChart($data, $legend, $dateRange));
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //注册审核趋势表格
    public function registerTrendTable(Request $request)
    {
        try {
            $data = $this->repository->registerTrendList($request);
            return result("", 0, $data["data"], $data["count"]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //网点地区分布数据
    public function serviceArea()
    {
        try {
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $agentsArr = agentIdAndProvinceId();
            $serviceArr = BlService::query()->pluck('agent_id', 'id')->toArray();
            $areaData = array_values_int($agentsMap);
            unset($areaData[209]);
            foreach ($serviceArr as $id => $agentId) {
                if (isset($agentsArr[$agentId]) && $agentsArr[$agentId] != 209) {
                    $areaData[$agentsArr[$agentId]]++;
                }
            }
            $replaceArea = [];
            foreach ($areaData as $id => $num) {
                if (isset($agentsMap[$id])) {
                    $replaceArea[$agentsMap[$id]] = $num;
                }
            }
            arsort($replaceArea);
            $categories = [];
            foreach ($replaceArea as $provinceName => $num) {
                $categories[] = $provinceName;
            }
            $data = [
                'categories' => $categories,
                'series'     => [
                    ['name' => '合作网点', 'type' => 'bar', 'data' => array_values($replaceArea)]
                ]
            ];
            return result("", 1, $data);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //网点数据按时间分布
    public function dataTrend($type)
    {
        if ($type == 'user') {
            $data = BlUser::query();
        } else {
            $data = BlService::query();
        }
        $data = $data->selectRaw("count(id) as num,date_format(created_at,'%Y') as year")
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('num', 'year')->toArray();
        if (!isset($data['2020'])) {
            $data['2020'] = 0;
        }

        $data = [
            'categories' => array_keys($data),
            'series'     => [
                ['type' => 'bar', 'data' => array_values($data)]
            ]
        ];
        return result("", 1, $data);
    }

    //租点-实时数据
    public function actualData($index)
    {
        $time = date('Y-m-d');
        $result = Cache::get('lease_actual_data');
        switch ($index) {
            case 1:
                $num = BlUser::query()->where('created_at', '>=', $time)->count();
                $data = [
                    [
                        'prefixText' => '今日新增用户(人)',
                        'data'       => $num
                    ]
                ];
                return result("", 1, $data);
            case 2:
                $num = BlService::query()->where('created_at', '>=', $time)->count();
                $data = [
                    [
                        'prefixText' => '今日新增网点(家)',
                        'data'       => $num
                    ]
                ];
                return result("", 1, $data);
            default:
                $data = [];
                if ($result) {
                    $data = $result[$index];
                }
                return result("", 1, $data);
        }
    }
}
