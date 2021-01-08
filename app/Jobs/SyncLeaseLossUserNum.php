<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlLeaseLost;
use Modules\Lease\Models\BlService;
use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\LeaseInsurance;
use Modules\Manage\Models\LeaseRenewal;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeaseService;


class SyncLeaseLossUserNum implements ShouldQueue
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


             LeaseInsurance::where("report_loss_user_num",null)->chunk(100, function ($users) {
                 foreach ($users as $k =>$v){
                     $time = strtotime($v->rent_date);
                     $end_time = date("Y-m-d 00:00:00", strtotime("+1 days", $time));
                     $start_time = date("Y-m-d 00:00:00", $time);

                     $syncLeaseContract =  BlLeaseLost::distinct()->where("created_at","<=",$end_time)
                         ->where("created_at",">",$start_time);



                     if ($v->province_id != 0){
                         $syncLeaseContract = $syncLeaseContract->where('province_id',$v->province_id);
                     }
                     $count = $syncLeaseContract->count("user_id");
                     LeaseInsurance::where('rent_date',$v->rent_date)->where('province_id',$v->province_id)->update(['report_loss_user_num'=>$count]);

                 }

            });

            systemLog("同步续租未处理订单任务成功");
        } catch (\Exception $e) {
            \Log::error("同步续租未处理订单任务失败: {$e->getMessage()}");
            systemLog("同步续租未处理订单任务失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步续租未处理订单任务失败：{$exception->getMessage()}");
        systemLog("同步续租未处理订单任务失败: 详情请见laravel-log");
    }
}
