<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Manage\Models\Report\LeaseService;
use Modules\Manage\Models\Service\LeaseServiceWithdraw;
use Modules\Manage\Models\Service\LeaseServiceWithdrawRate;

class SyncLeaseServiceWithdrawRate implements ShouldQueue
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

            $maxDate = LeaseServiceWithdrawRate::max("created_at");

            $begin = $end = date("Y-m-d");
            if (!$maxDate) {
                $firstLog = LeaseServiceWithdraw::query()
                    ->min("created_at");
                $begin = $firstLog ? date("Y-m-d", strtotime($firstLog)) : $begin;
            } else {
                $begin = $maxDate;
            }

            $defaultDay = [
                "begin" => $begin,
                "end"   => $end . " 23:59:59"
            ];


            $service_list_id = LeaseServiceWithdraw::where("created_at",'>', $defaultDay['begin'])
                ->where("created_at",'<', $defaultDay['end'])
                ->groupBy('service_id')
                ->orderBy('created_at','desc')
                ->pluck('service_id')
                ->toArray();


            if (!empty($service_list_id)){
                $list = LeaseServiceWithdraw::select("created_at","province_id","service_id","agent_id")->whereIn('service_id',$service_list_id)
                    ->orderBy('created_at','desc')->get()->toArray();

                $rate_data = $data = [];

                foreach ($service_list_id as $key =>$val){
                    foreach ($list as $k1 =>$v1) {
                        if ($val == $v1['service_id'])
                            $data[$val][] = $v1;
                    }


                }

                foreach($data as $key =>$val){
                    if (!empty($val)) {
                        foreach ($val as $k => $v) {
                            $n = count($val) - 1;
                            if ($n > $k) {
                                $rate_data[$key][$k]['rate_num'] = intval(ceil((strtotime($data[$key][$k]['created_at']) - strtotime($data[$key][$k + 1]['created_at'])) / 86400));
                                $rate_data[$key][$k]['province_id'] = $v['province_id'];
                                $rate_data[$key][$k]['service_id'] = $v['service_id'];
                                $rate_data[$key][$k]['created_at'] = $v['created_at'];
                                $rate_data[$key][$k]['agent_id'] = $v['agent_id'];
                            }

                        }

                    }

                }

                //覆盖删除
                foreach ($rate_data as $k =>$v){
                    LeaseServiceWithdrawRate::where('service_id','=',$k)->delete();
                    LeaseServiceWithdrawRate::insert($v);

                }


            }


            systemLog("同步网点提现频率成功");
        } catch (\Exception $e) {
            \Log::error("同步网点提现频率失败: {$e->getMessage()}");
            systemLog("同步网点提现频率失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步网点提现频率失败：{$exception->getMessage()}");
        systemLog("同步网点提现频率失败: 详情请见laravel-log");
    }
}
