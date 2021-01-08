<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlService;
use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Report\LeaseServiceRetrieve;
use Modules\Manage\Models\Report\LeaseServiceStockCancel;
use Modules\Manage\Models\Report\LeaseServiceSupplie;


class SyncLeaseServiceCancel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    //最多运行5次
    public $tries = 5;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            //获取数据库最大的时间   LeaseServiceStockCancel::max("date");
            $maxCreatedTime = LeaseServiceStockCancel::max("date");
            if (!$maxCreatedTime) {$maxCreatedTime = '2018-9-30';}
            $begin = $end = date("Y-m-d", strtotime("-1 day"));
            $agentsMap = syncProvincesName(allLeaseProvinces());
            $agentsMap[0] = '总数';
            //时间数据 从当前数据库最大的时间的后一天，到系统的昨天   2019-8-1 ~ 2018-8-12
            $dateArr = getDateRange($maxCreatedTime, $end);   //[0 => "2019-08-12"]
            //服务器是今天运行昨天的数据，所以要减去一天
            $maxCreatedTimeend = date("Y-m-d");
            $province_ids = [65, 78, 85, 91, 110, 118, 129, 132, 145, 209, 214];
            //补货总数量
            $data = LeaseServiceSupplie::query()->from("lease_service_supplies as a")
                ->selectRaw("a.date,b.province_id,SUM(a.num) as nums")
                ->leftJoin("lease_services as b", "a.service_id", "=", "b.id")
                ->whereBetween("a.date", [$maxCreatedTime, $maxCreatedTimeend])
                ->Groupby("b.province_id")
                ->Groupby("a.date")
                ->orderbyRaw("a.date desc")
                ->get()->toArray();
            
            if ($data) {
                $res = [];
                foreach ($dateArr as $k => $v) {
                    //循环定义变量
                    foreach ($province_ids as $i) {
                        $res[$v]['num_' . $i] = 0;
                    }
                    $res[$v]['total_num'] = 0;
                    $res[$v]['systemtype'] = 1;
                    $res[$v]['type'] = 1;
                    $res[$v]['date'] = $v;
                    foreach ($data as $key => $value) {
                        if ($value['province_id'] != null) {
                            if ($value['date'] == $v) {
                                $res[$v]['num_' . $value['province_id']] = $value['nums'];
                                $res[$v]['total_num'] += $value['nums'];
                            }
                        }
                    }
                    
                }
                sort($res);
                LeaseServiceStockCancel::query()->insert($res);
            }
            
            
            //退货总数量
            $data1 = LeaseServiceRetrieve::query()->from("lease_service_retrieves as a")
                ->selectRaw("a.date,b.province_id,SUM(a.num) as nums")
                ->leftJoin("lease_services as b", "a.service_id", "=", "b.id")
                ->whereBetween("a.date", [$maxCreatedTime, $maxCreatedTimeend])
                ->where('lease_service_retrieves.status', 2)
                ->Groupby("b.province_id")
                ->Groupby("a.date")
                ->orderbyRaw("a.date desc")
                ->get()->toArray();
            
            if ($data1) {
                $res = [];
                foreach ($dateArr as $k => $v) {
                    //循环定义变量
                    foreach ($province_ids as $i) {
                        $res[$v]['num_' . $i] = 0;
                    }
                    $res[$v]['total_num'] = 0;
                    $res[$v]['systemtype'] = 2;
                    $res[$v]['type'] = 1;
                    $res[$v]['date'] = $v;
                    foreach ($data1 as $key => $value) {
                        if ($value['province_id'] != null) {
                            if ($value['date'] == $v) {
                                $res[$v]['num_' . $value['province_id']] = $value['nums'];
                                $res[$v]['total_num'] += $value['nums'];
                            }
                        }
                    }
                    
                }
                sort($res);
                LeaseServiceStockCancel::query()->insert($res);
            }
            
            
            //回收总数量
            $data2 = LeaseServiceRetrieve::query()->from("lease_service_retrieves as a")
                ->selectRaw("a.date,b.province_id,SUM(a.num) as nums")
                ->leftJoin("lease_services as b", "a.service_id", "=", "b.id")
                ->whereBetween("a.date", [$maxCreatedTime, $maxCreatedTimeend])
                ->where('a.status', 3)
                ->Groupby("b.province_id")
                ->Groupby("a.date")
                ->orderbyRaw("a.date desc")
                ->get()->toArray();
            
            if ($data2) {
                $res = [];
                foreach ($dateArr as $k => $v) {
                    //循环定义变量
                    foreach ($province_ids as $i) {
                        $res[$v]['num_' . $i] = 0;
                    }
                    $res[$v]['total_num'] = 0;
                    $res[$v]['systemtype'] = 3;
                    $res[$v]['type'] = 1;
                    $res[$v]['date'] = $v;
                    foreach ($data2 as $key => $value) {
                        if ($value['province_id'] != null) {
                            if ($value['date'] == $v) {
                                $res[$v]['num_' . $value['province_id']] = $value['nums'];
                                $res[$v]['total_num'] += $value['nums'];
                            }
                        }
                    }
                    
                }
                sort($res);
                LeaseServiceStockCancel::query()->insert($res);
            }
            
            
            //补货总申请数
            $data3 = LeaseServiceSupplie::query()->from("lease_service_supplies as a")
                ->selectRaw("a.date,b.province_id,COUNT(a.id) as nums")
                ->leftJoin("lease_services as b", "a.service_id", "=", "b.id")
                ->whereBetween("a.date", [$maxCreatedTime, $maxCreatedTimeend])
                ->Groupby("b.province_id")
                ->Groupby("a.date")
                ->orderbyRaw("a.date desc")
                ->get()->toArray();
            
            if ($data3) {
                $res = [];
                foreach ($dateArr as $k => $v) {
                    //循环定义变量
                    foreach ($province_ids as $i) {
                        $res[$v]['num_' . $i] = 0;
                    }
                    $res[$v]['total_num'] = 0;
                    $res[$v]['systemtype'] = 1;
                    $res[$v]['type'] = 2;
                    $res[$v]['date'] = $v;
                    foreach ($data3 as $key => $value) {
                        if ($value['province_id'] != null) {
                            if ($value['date'] == $v) {
                                $res[$v]['num_' . $value['province_id']] = $value['nums'];
                                $res[$v]['total_num'] += $value['nums'];
                            }
                        }
                    }
                    
                }
                sort($res);
                LeaseServiceStockCancel::query()->insert($res);
            }
            
            
            //退货总申请数
            $data4 = LeaseServiceRetrieve::query()->from("lease_service_retrieves as a")
                ->selectRaw("a.date,b.province_id,COUNT(a.id) as nums")
                ->leftJoin("lease_services as b", "a.service_id", "=", "b.id")
                ->whereBetween("a.date", [$maxCreatedTime, $maxCreatedTimeend])
                ->where('a.status', 2)
                ->Groupby("b.province_id")
                ->Groupby("a.date")
                ->orderbyRaw("a.date desc")
                ->get()->toArray();
            
            if ($data4) {
                $res = [];
                foreach ($dateArr as $k => $v) {
                    //循环定义变量
                    foreach ($province_ids as $i) {
                        $res[$v]['num_' . $i] = 0;
                    }
                    $res[$v]['total_num'] = 0;
                    $res[$v]['systemtype'] = 2;
                    $res[$v]['type'] = 2;
                    $res[$v]['date'] = $v;
                    foreach ($data4 as $key => $value) {
                        if ($value['province_id'] != null) {
                            if ($value['date'] == $v) {
                                $res[$v]['num_' . $value['province_id']] = $value['nums'];
                                $res[$v]['total_num'] += $value['nums'];
                            }
                        }
                    }
                    
                }
                sort($res);
                LeaseServiceStockCancel::query()->insert($res);
            }
            
            
            //回收总申请数
            $data5 = LeaseServiceRetrieve::query()->from("lease_service_retrieves as a")
                ->selectRaw("a.date,b.province_id,COUNT(a.id) as nums")
                ->leftJoin("lease_services as b", "a.service_id", "=", "b.id")
                ->whereBetween("a.date", [$maxCreatedTime, $maxCreatedTimeend])
                ->where('a.status', 3)
                ->Groupby("b.province_id")
                ->Groupby("a.date")
                ->orderbyRaw("a.date desc")
                ->get()->toArray();
            
            if ($data5) {
                $res = [];
                foreach ($dateArr as $k => $v) {
                    //循环定义变量
                    foreach ($province_ids as $i) {
                        $res[$v]['num_' . $i] = 0;
                    }
                    $res[$v]['total_num'] = 0;
                    $res[$v]['systemtype'] = 3;
                    $res[$v]['type'] = 2;
                    $res[$v]['date'] = $v;
                    foreach ($data5 as $key => $value) {
                        if ($value['province_id'] != null) {
                            if ($value['date'] == $v) {
                                $res[$v]['num_' . $value['province_id']] = $value['nums'];
                                $res[$v]['total_num'] += $value['nums'];
                            }
                        }
                    }
                }
                sort($res);
                LeaseServiceStockCancel::query()->insert($res);
            }
            
            
            systemLog("同步网点每日补货/退货/回收统计的任务成功");
        } catch (\Exception $e) {
            \Log::error("同步网点每日补货/退货/回收统计的任务失败: {$e->getMessage()}");
            systemLog("同步网点每日补货/退货/回收统计的任务失败: 详情请见laravel-log");
        }
    }
    
    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步网点每日补货/退货/回收统计的任务失败：{$exception->getMessage()}");
        systemLog("同步网点每日补货/退货/回收统计的任务失败: 详情请见laravel-log");
    }
}
