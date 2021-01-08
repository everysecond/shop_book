<?php

namespace Modules\Manage\Http\Controllers\Lease;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Lease\Models\BlUser;
use Modules\Lease\Models\BlUserInsurance;
use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseRegisterLog;
use Modules\Manage\Models\Report\LeaseUser;

class ReportController extends Controller
{
    protected $leaseUserModel;
    protected $ageArr = ["30岁以下", "31-40岁", "41-50岁", "50岁以上"];
    protected $sexArr = ["男", "女"];
    protected $hourArr = ["0:00", "1:00", "2:00", "3:00", "4:00", "5:00", "6:00", "7:00", "8:00",
        "9:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00",
        "19:00", "20:00", "21:00", "22:00", "23:00"];
    protected $registerType = [
        "1" => "手机注册",
        "2" => "微信注册",
        "3" => "QQ注册",
        "4" => "支付宝注册",
        "5" => "微信小程序注册"
    ];

    public function __construct(LeaseUser $leaseUserModel)
    {
        $this->leaseUserModel = $leaseUserModel;
    }

    public function order()
    {
        return view("lease::report.order");
    }

    //用户画像view
    public function portrayal()
    {
        $provinces = allUserProvinces("all");
        return view("manage::lease.report.user.portrayal", compact("provinces"));
    }

    //注册数据view
    public function register()
    {
        $provinces = allUserProvinces("all");
        $provinces2 = allUserProvinces(0);
        $timeType = dateType();
        return view("manage::lease.report.user.register_chart", compact("provinces", "provinces2", "timeType"));
    }

    //用户年龄分布数据
    public function age(Request $request)
    {
        try {
            $req = $request->all();
            $sql = "elt(interval(age,0,31,41,51),'30岁以下','31-40岁','41-50岁','50岁以上')";
            $ageData = $this->leaseUserModel->selectRaw($sql . " as age_area,count(id) as num");
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $ageData->where("province_id", $req["agentId"]);
            }
            $ageData->where("age", ">", 0);
            $ageData->groupBy("age_area");
            $ageData = $ageData->pluck("num", "age_area")->toArray();
            $ageDataFormat = [];
            $ageArr = [];
            foreach ($this->ageArr as $item) {
                if (isset($ageData[$item]) && $ageData[$item] > 0) {
                    $ageArr[] = $item;
                    $ageDataFormat[] = [
                        "value" => isset($ageData[$item]) ? $ageData[$item] : 0,
                        "name"  => $item
                    ];
                }
            }
            $returnData = [
                "ageArr"  => $ageArr,
                "ageData" => $ageDataFormat
            ];
            if (empty($ageArr)) {
                $returnData = [
                    "ageArr"  => ["暂无数据"],
                    "ageData" => [[
                        "value" => 0,
                        "name"  => "暂无数据"
                    ]]
                ];
            }
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            Log::error('年龄分布错误:', $exception->getMessage());
            return result($exception->getMessage(), -1);
        }
    }

    //用户性别分布数据
    public function sex(Request $request)
    {
        try {
            $req = $request->all();
            $sexData = $this->leaseUserModel->selectRaw("sex, count(id) as num ");
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $sexData->where("province_id", $req["agentId"]);
            }
            $sexData->whereNotNull("sex");
            $sexData->groupBy("sex");
            $sexData = $sexData->pluck("num", "sex")->toArray();
            $sexDataFormat = [];
            foreach ($this->sexArr as $item) {
                $value = 0;
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
            $returnData = [
                "sexArr"  => $this->sexArr,
                "sexData" => $sexDataFormat
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            Log::error('性别分布错误:', $exception->getMessage());
            return result($exception->getMessage(), -1);
        }
    }

    //用户手机型号分布数据
    public function mobileModel(Request $request)
    {
        try {
            $req = $request->all();
            $modelData = CrmUser::query()->selectRaw("is_auth, count(id) as num ")
                ->where('cus_type', CrmUser::CUS_TYPE_ONE)
                ->whereIn('is_auth', [0, 1, 2]);
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $modelData->where("province_id", $req["agentId"]);
            }
            $modelData->groupBy("is_auth")->orderBy('is_auth');
            $modelData = $modelData->pluck("num", "is_auth")->toArray();
            $modelDataFormat = [];
            foreach ($modelData as $model => $num) {
                $modelDataFormat[] = [
                    "value" => $num,
                    "name"  => $model == 0 ? '未认证' : ($model == 1 ? '已认证' : '审核中'),
                ];
            }
            $returnData = [
                "modelArr"  => ['未认证', '已认证', '审核中'],
                "modelData" => $modelDataFormat
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            Log::error('用户认证数据分布错误:', $exception->getMessage());
            return result($exception->getMessage(), -1);
        }
    }

    //用户地区分布数据
    public function areaData()
    {
        try {
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $provincesArr = getAllProvincesName();
            $areaData = $this->leaseUserModel->selectRaw("province_id, count(id) as num ");
            $areaData->where("province_id", ">", 0)->groupBy("province_id");
            $areaData = $areaData->pluck("num", "province_id")->toArray();
            $max = max($areaData);
            $min = min($areaData);
            $replaceArea = [];
            foreach ($areaData as $id => $num) {
                if (isset($agentsMap[$id])) {
                    $replaceArea[$agentsMap[$id]] = $num;
                }
            }
            $areaDataFormat = [];
            foreach ($provincesArr as $name) {
                $value = isset($replaceArea[$name]) ? $replaceArea[$name] : 0;
                $areaDataFormat[] = [
                    "value" => $value,
                    "name"  => $name
                ];
            }
            $returnData = [
                "max"      => $max,
                "min"      => 0,
                "areaData" => $areaDataFormat,
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //租赁次数
    public function leaseTime(Request $request)
    {
        try {
            $req = $request->all();
            $userCount = app(BlUser::class);
            $userContractCount = LeaseContract::selectRaw(" count(id) as times ");
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $userContractCount->where("province_id", $req["agentId"]);
                $userCount = $userCount->where("province_id", $req["agentId"]);
            }
            $userCount = $userCount->count();
            $userContractCount = $userContractCount->whereIn("status", [3, 4, 5, 7, 8])->groupBy("user_id")->pluck("times");
            $timeZero = $userCount - count($userContractCount);
            $xAxis = [0 => "0次"];
            $dataArr = [0 => $timeZero];
            foreach ($userContractCount as $count) {
                if (array_key_exists($count, $dataArr)) {
                    $dataArr[$count]++;
                } else {
                    $dataArr[$count] = 1;
                    $xAxis[$count] = $count . "次";
                }
            }
            ksort($dataArr);
            ksort($xAxis);
            $returnData = [
                "xAxis"      => array_values($xAxis),
                "seriesData" => array_values($dataArr)
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            Log::error('租赁次数报错：', $exception->getMessage());
            return result($exception->getMessage(), -1);
        }
    }

    //租赁周期
    public function leaseTerm(Request $request)
    {
        try {
            $req = $request->all();
            $contracts = LeaseContract::selectRaw(" count(id) as count,contract_term ");
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $contracts->where("province_id", $req["agentId"]);
            }
            $contracts->whereIn("status", [3, 4, 5, 7, 8])->groupBy("contract_term")->orderBy("contract_term");
            $xAxis = [];
            $dataArr = [];
            foreach ($contracts->pluck("count", "contract_term") as $term => $count) {
                $xAxis[] = $term . "个月";
                $dataArr[] = $count;
            }
            $returnData = [
                "xAxis"      => array_values($xAxis),
                "seriesData" => array_values($dataArr)
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            Log::error('租赁周期错误：', $exception->getMessage());
            return result($exception->getMessage(), -1);
        }
    }

    //用户每小时注册对比
    public function registerHour(Request $request)
    {
        try {
            $req = $request->all();
            $defaultDay = [
                date("Y-m-d", strtotime("-1 day")),
                date("Y-m-d")
            ];
            if (isset($req["dayArr"]) && $req["dayArr"]) {
                $defaultDay = array_merge($req["dayArr"], $defaultDay);
            }
            $data = LeaseRegisterLog::select("date", "register_num_str", "total");
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $data->where("province_id", $req["agentId"]);
            } else {
                $data->where("province_id", 0);
            }
            $data->whereIn("date", $defaultDay)->where("type", LeaseRegisterLog::LOG_TYPE_ONE);
            $data = $data->get()->toArray();
            $series = [];
            $defaultNumArr = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($defaultDay as $k => $day) {
                $series[$k] = [
                    "name"       => $day,
                    "type"       => 'line',
                    "stack"      => $day,
                    "symbolSize" => 6,
                    "symbol"     => 'circle',
                    "data"       => $defaultNumArr,
//                    "smooth"     => true,
//                    "itemStyle"=> ["normal"=> ["areaStyle"=> ["type"=> 'default']]],
                ];
                foreach ($data as $item) {
                    if ($item["date"] == $day) {
                        $numArr = explode(",", $item["register_num_str"]);
                        $numArr = array_chunk($numArr, 24)[0];
                        $series[$k]["data"] = $numArr;
                    }
                }
            }
            $returnData = [
                "days"    => $defaultDay,
                "hourArr" => $this->hourArr,
                "series"  => $series,
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //用户每小时累计注册对比
    public function registerHourSum(Request $request)
    {
        try {
            $req = $request->all();
            $defaultDay = [
                date("Y-m-d", strtotime("-1 day")),
                date("Y-m-d")
            ];
            if (isset($req["dayArr"]) && $req["dayArr"]) {
                $defaultDay = array_merge($req["dayArr"], $defaultDay);
            }
            $data = LeaseRegisterLog::select("date", "register_num_str", "total");
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $data->where("province_id", $req["agentId"]);
            } else {
                $data->where("province_id", 0);
            }
            $data->whereIn("date", $defaultDay)->where("type", LeaseRegisterLog::LOG_TYPE_TWO);
            $data = $data->get()->toArray();
            $series = [];
            $defaultNumArr = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($defaultDay as $k => $day) {
                $series[$k] = [
                    "name"       => $day,
                    "type"       => 'line',
                    "stack"      => $day,
                    "symbolSize" => 6,
                    "symbol"     => 'circle',
                    "data"       => $defaultNumArr
                ];
                foreach ($data as $item) {
                    if ($item["date"] == $day) {
                        $numArr = explode(",", $item["register_num_str"]);
                        $numArr = array_chunk($numArr, 24)[0];
                        $series[$k]["data"] = $numArr;
                    }
                }
            }
            $returnData = [
                "days"    => $defaultDay,
                "hourArr" => $this->hourArr,
                "series"  => $series,
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    /**
     * 用户每小时注册统计
     * @param Request $request
     *        type 1 当天
     *        type 2 累计到当天
     * @return false|string
     */
    public function tableRegisterHour(Request $request)
    {
        try {
            $data = LeaseRegisterLog::getList($request);
            return result("", 0, $data["data"], $data["count"]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //每日注册趋势
    public function registerDay(Request $request)
    {
        try {
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 day")),
                "end"   => date("Y-m-d")
            ];
            $data = LeaseRegisterLog::registerDay($request, $defaultDay);
            $numData = [];
            $actuallyBegin = isset($data[0]) ? $data[0]["date"] : $defaultDay["begin"];
            foreach ($data as $datum) {
                $numData[] = $datum["num"];
            }

            $returnData = [
                "hourArr" => getDateRange($actuallyBegin, $defaultDay["end"]),
                "numData" => $numData,
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //用户注册渠道分布数据
    public function registerFrom(Request $request)
    {
        try {
            $req = $request->all();
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-6 day")),
                "end"   => date("Y-m-d")
            ];
            if (isset($req["days"]) && $req["days"] != -1) {
                $days = $req["days"];
                $defaultDay = [
                    "begin" => date("Y-m-d", strtotime("-$days day")),
                    "end"   => date("Y-m-d")
                ];
            } else if (isset($req["days"]) && $req["days"] == -1 && isset($req["dateRange"]) && $req["dateRange"]) {
                $days = explode(" - ", $req["dateRange"]);
                $defaultDay = [
                    "begin" => $days[0],
                    "end"   => $days[1]
                ];
            }

            $modelData = $this->leaseUserModel->selectRaw("register_type, count(id) as num ");
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $modelData->where("province_id", $req["agentId"]);
            }
            $modelData->whereBetween("register_at", $defaultDay)->groupBy("register_type");
            $modelData = $modelData->pluck("num", "register_type")->toArray();
            $modelDataFormat = [];
            $modelArr = [];
            $registerTypeArr = $this->registerType;
            foreach ($modelData as $model => $num) {
                if ($model) {
                    $name = isset($registerTypeArr[$model]) ? $registerTypeArr[$model] : "";
                    $modelArr[] = $name;
                    $modelDataFormat[] = [
                        "value" => $num,
                        "name"  => $name
                    ];
                }
            }
            $returnData = [
                "modelArr"  => $modelArr,
                "modelData" => $modelDataFormat
            ];
            return result("", 1, $returnData);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //注册统计
    public function registerTotal(Request $request)
    {
        try {
            $data = LeaseRegisterLog::getSumData($request);
            return result("", 0, $data["data"], $data["count"]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //用户地区分布数据
    public function userArea(Request $request)
    {
        try {
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $areaData = BlUser::query()->selectRaw("province_id, count(id) as num ");
            $areaData->where("province_id", ">", 0)->where("province_id", "<>", 209)->groupBy("province_id");
            $areaData = $areaData->pluck("num", "province_id")->toArray();
            $replaceArea = [];
            foreach ($areaData as $id => $num) {
                if (isset($agentsMap[$id])) {
                    $replaceArea[$agentsMap[$id]] = $num;
                }
            }
            asort($replaceArea);
            $categories = [];
            foreach ($replaceArea as $provinceName => $num) {
                $categories[] = $provinceName;
            }
            $data = [
                'categories' => $categories,
                'series'     => [['name' => '车主用户','data' => array_values($replaceArea)]]
            ];
            $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
            return response(['data' => $data], 200, ['Access-Control-Allow-Origin' => $origin]);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    //数据大屏 实名认证
    public function userAuth(Request $request)
    {
        try {
            $req = $request->all();
            $modelData = CrmUser::query()->selectRaw("is_auth, count(id) as num ")
                ->where('cus_type', CrmUser::CUS_TYPE_ONE)
                ->whereIn('is_auth', [0, 1, 2]);
            $insuredCount = BlUserInsurance::query()->selectRaw(" DISTINCT user_id ")
                ->where('status', 20)->count();
            if (array_key_exists("agentId", $req) && $req["agentId"] != "all") {
                $modelData->where("province_id", $req["agentId"]);
            }
            $modelData->groupBy("is_auth")->orderBy('is_auth');
            $modelData = $modelData->pluck("num", "is_auth")->toArray();
            $modelDataFormat = [];
            $total = array_sum($modelData);
            foreach ($modelData as $model => $num) {
                if ($model == 0) {
                    $num -= $insuredCount;
                } elseif ($model == 1) {
                    $num += $insuredCount;
                }

                $rate = "\r\n" . round($num / $total * 100, 3) . '%';
                $modelDataFormat[] = [
                    "value" => $num,
                    "name"  => ($model == 0 ? '未认证' : ($model == 1 ? '已认证' : '审核中')) . $rate,
                ];
            }
            return result("", 1, $modelDataFormat);
        } catch (\Exception $exception) {
            Log::error('用户认证数据分布错误:', $exception->getMessage());
            return result($exception->getMessage(), -1);
        }
    }

}
