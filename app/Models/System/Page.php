<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Page extends Model
{
    protected $table = "pages";
    public $timestamps = false;

    //获取知识列表
    public function getPageLists($request){
        $page = isset($request->page) ? $request->page : 0;
        $pageSize = isset($request->page_size) ? $request->page_size : 15;
        $pageStart = ($page-1) * $pageSize;
        $query = DB::table($this->table);
        $datatime =  $request->datetime;
        //按指定时间进行查询
        if($request->datetime!=""){
            $floatStartDate = substr($datatime,0,10);
            $startDate = $floatStartDate." 00:00:00";
            $floatEndDate   = substr($datatime,-10);
            $endDate   = $floatEndDate." 00:00:00";
            //处理时间包含开始时间和结束时间
            $startDateStr = strtotime($startDate) - 1;
            $endDateStr   = strtotime($endDate) + (24*3600-1);

            $startDate = date("Y-m-d H:i:s",$startDateStr);
            $endDate   = date("Y-m-d H:i:s",$endDateStr);
            $query->whereBetween('create_date', [$startDate, $endDate]);
        }
        //按关键词进行查询
        if($request->keyword!=""){
            $keyword = $request->keyword;
            $query->where(function($query)use ($keyword){
                $query->where('title','like','%'.$keyword.'%')
                    ->orWhere('description','like','%'.$keyword.'%');
            });
        }
        $data['count'] = $query->count();
        $data['lists'] = $query->orderBy("create_date","DESC")->offset($pageStart)->limit($pageSize)->get();
        return $data;
    }

    //获取知识详情
    public function getPageInfo($id){
        return DB::table($this->table)->where("id",$id)->first();
    }

    //编辑知识
    public function pageUpdate($request){

        $editData = $request->except('_token','id');
        DB::table($this->table)->where("id","=",$request->id)->update($editData);
        return returnData("更新成功！",1);
    }
}
