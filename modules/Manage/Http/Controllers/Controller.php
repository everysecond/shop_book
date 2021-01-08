<?php

namespace Modules\Manage\Http\Controllers;

use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Manage\Models\Log;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {

    }

    public function auth()
    {
        return Auth::guard('manage');
    }

    public function success($message = 'success', $data = [])
    {
        return $this->response($message, 0, $data);
    }

    public function error($message, $code = 1000, $data = [])
    {
        return $this->response($message, $code, $data);
    }

    public function response($msg, $code = 0, $data = [])
    {
        return compact('code', 'msg', 'data');
    }

    public function view($view = null, $data = [], $mergeData = [])
    {
        return view('manage::' . $view, $data, $mergeData);
    }

    //时间参数格式化处理
    public function formatDateRange($request)
    {
        $defaultDay = [
            "begin" => date("Y-m-d", strtotime("-6 day")),
            "end"   => date("Y-m-d")
        ];
        $req = $request->all();
        if (isset($req["days"]) && $req["days"] != -1) {
            $days = $req["days"];
            $defaultDay = [
                "begin" => date("Y-m-d", strtotime("-$days day")),
                "end"   => date("Y-m-d")
            ];
        } elseif (isset($req["days"]) && $req["days"] == -1 && isset($req["dateRange"]) && $req["dateRange"]) {
            $days = explode(" - ", $req["dateRange"]);
            $defaultDay = [
                "begin" => $days[0],
                "end"   => $days[1]
            ];
        }
        return $defaultDay;
    }

    public function actionLog($message, $ip = '127.0.0.1')
    {
        $log = new Log();
        $log->writeLog($message, $ip);
    }

    /*
     * api Resources data
     * @param $msg 响应结果
     * @param $code 响应代码
     * @param $data 响应数据
     * @param $apilogId 请求日志ID  0 1 1002
     */
    public function Resources($msg, $code = 0, $data = "")
    {
        $res = array();
        $res['msg'] = $msg;
        $res['code'] = $code;
        $res['data'] = $data;
        return json_encode($res);
    }

    public function timeBegin($type = 1)
    {
        if($type == 1){//今日
            return date('Y-m-d');
        } elseif ($type == 2) {//本月
            return date('Y-m').'-01';
        } elseif ($type == 3) {
            return date('Y').'-01-01';
        } else {
            return null;
        }
    }

    public function timeSelect($type = 1)
    {
        if($type == 1){//近一周
            return date('Y-m-d',strtotime('-6 days'));
        } elseif ($type == 2) {//近一月
            return date('Y-m-d',strtotime('-29 days'));
        } elseif ($type == 3) {//近半年
            return date('Y-m-d',strtotime('-182 days'));
        } elseif ($type == 4) {//近一年
            return date('Y-m-d',strtotime('-364 days'));
        } else {
            return null;
        }
    }

    public function transformSite($data)
    {
        $sites = siteCache();
        foreach ($data as &$datum) {
            $datum['name'] = Arr::get($sites,$datum['site_id']);
        }
        return $data;
    }
}
