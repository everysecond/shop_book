<?php

namespace App\Models\System;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Log extends Model
{
    protected $table = "admin_logs";
    public $timestamps = false;
    
    //获取访问日志列表
    public function getLogLists($request, $type = "lists")
    {
        $page = isset($request->page) ? $request->page : 0;
        $pageSize = isset($request->page_size) ? $request->page_size : 15;
        $pageStart = ($page - 1) * $pageSize;
        $query = DB::table($this->table);
        $datatime = $request->datetime;
        //按指定时间进行查询
        if ($request->datetime != "") {
            //echo $datatime;
            $floatStartDate = substr($datatime, 0, 10);
            $startDate = $floatStartDate . " 00:00:00";
            $floatEndDate = substr($datatime, -10);
            $endDate = $floatEndDate . " 00:00:00";
            if (strtotime($startDate) > time()) {
            
            } elseif (strtotime($startDate) > strtotime($endDate)) {
            
            } else {
                //处理时间包含开始时间和结束时间
                $startDateStr = strtotime($startDate) - 1;
                $endDateStr = strtotime($endDate) + (24 * 3600 - 1);
                
                $startDate = date("Y-m-d H:i:s", $startDateStr);
                $endDate = date("Y-m-d H:i:s", $endDateStr);
                $query->whereBetween('create_date', [$startDate, $endDate]);
            }
        }
        //按关键词进行查询
        if ($request->keyword != "") {
            $keyword = $request->keyword;
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('roles', 'like', '%' . $keyword . '%')
                    ->orWhere('content', 'like', '%' . $keyword . '%');
            });
        }
        if ($type == "lists") {
            $data['count'] = $query->count();
            $data['lists'] = $query->orderBy("create_date", "DESC")->offset($pageStart)->limit($pageSize)->get();
            return $data;
        } elseif ($type == "total") {
            return $query->count();
        } elseif ($type == "all") {
            return $query->orderBy("create_date", "DESC")->get();
        }
    }
    
    public function writeLog($message)
    {
        $adminInfo = Session::get("adminInfo");
        $addData = array(
            "name" => $adminInfo->realname,
            "roles" => $adminInfo->role_name,
            "content" => $message,
            "create_date" => date("Y-m-d H:i:s")
        );
        self::insertGetId($addData);
    }
}

