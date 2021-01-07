<?php

namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlLeaseContract;
use Modules\Lease\Models\BlLeaseLost;
use Modules\Lease\Models\BlLeasePayment;
use Carbon\Carbon;
use Modules\Lease\Models\BlUserInsurance;
use Modules\Manage\Models\LeaseInsurance;
use Modules\Manage\Models\LeaseRenewal;

class SyncLeaseInsurance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //最多运行5次
    public $tries = 5;

    /**
     * 该处处理成数组是为了兼容其他地方的使用
     * Create a new job instance.
     * @param array $user eg: ['email' => '121@123.com', 'name' => 'job']
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
            $provinces = allLeaseProvinces();
            $provinces[0] = '全部区域';

            foreach ($provinces as $k =>$v){
                $renewal_insurance_num[$k] = 0;
                $rent_insurance_num[$k] = 0;
                $rent_num[$k] = 0;
                $renewal_num[$k]= 0;
                $report_lost_num[$k] = 0;
            }

            //昨日续租
            $time = selectTimeRange(8);
            $list = [];
            $renewal_list = BlLeasePayment::where("payed_at","<=",$time['end_time'])->where("payed_at",">",$time['start_time'])->where("status","=",1)->whereIn("type",array('2','3'))
                ->get()->toArray();

            $contract_id = array_column($renewal_list,'contract_id');
            $renewal_insurance_list = BlUserInsurance::select('province_id','contract_no')
                ->where("status","=",20)
                ->whereIn("contract_id",$contract_id)->get()->toArray();

            if (!empty($renewal_insurance_list)){
                //续租投保数
                foreach ($provinces as $k=>$v){
                    foreach ($renewal_insurance_list as $key=>$value){
                        if ($k==$value['province_id']){
                            ++$renewal_insurance_num[$k];
                            ++$renewal_insurance_num[0];
                        }
                    }
                }
            }

            $rent_list = BlLeasePayment::where("payed_at","<=",$time['end_time'])->where("payed_at",">",$time['start_time'])
                ->where("status","=",1)->whereIn("type",array('1'))
                ->get()->toArray();
            $rent_contract_id = array_column($rent_list,'contract_id');
            $rent_insurance_list = BlUserInsurance::select('province_id','contract_no')
                ->where("status","=",20)
                ->whereIn("contract_id",$rent_contract_id)->get()->toArray();

            if (!empty($rent_insurance_list)){
                //新租（租赁）投保数
                foreach ($provinces as $k=>$v){
                    foreach ($rent_insurance_list as $key=>$value){
                        if ($k==$value['province_id']){
                            ++$rent_insurance_num[$k];
                            ++$rent_insurance_num[0];
                        }
                    }
                }
            }


            if (!empty($rent_list)){
                //新租（租赁）数
                foreach ($provinces as $k=>$v){
                    foreach ($rent_list as $key=>$value){
                        if ($k==$value['province_id']){
                            ++$rent_num[$k];
                            ++$rent_num[0];
                        }
                    }
                }
            }

            //续租数
            $renewal_num = LeaseRenewal::selectRaw("renewal_num,province_id")
                ->where('type','=',1)
                ->where("renewal_date","<=",$time['end_time'])
                ->where("renewal_date",">=",$time['start_time'])
                ->pluck('renewal_num','province_id')->toArray();

            //报失数
            $report_lost_num = BlLeaseLost::selectRaw("count(id) as lost_num,province_id")
                ->where("created_at","<=",$time['end_time'])
                ->where("created_at",">=",$time['start_time'])
                ->groupby('province_id')
                ->pluck('lost_num','province_id')->toArray();

            //报失总数
            $report_lost_total= 0;
            foreach ($report_lost_num as $k1 =>$v1){
                $report_lost_total += $v1;
            }


            foreach ($provinces as $k2=>$v3){
                $data[$k2]['rent_date'] = Carbon::yesterday();
                $data[$k2]['created_at'] = time();
                $data[$k2]['province_id'] = $k2;
                $data[$k2]['renewal_insure_num'] = $renewal_insurance_num[$k2];
                $data[$k2]['type'] = 1;
                $data[$k2]['rent_insure_num'] = $rent_insurance_num[$k2];
                $data[$k2]['rent_num'] = $rent_num[$k2];
                if (empty($renewal_num[$k2])) $renewal_num[$k2] = 0;
                $data[$k2]['renewal_num'] = $renewal_num[$k2];
                $data[$k2]['insure_num'] = $rent_insurance_num[$k2] + $renewal_insurance_num[$k2];
                $data[$k2]['uninsured_num'] = $rent_num[$k2]+$renewal_num[$k2]-$rent_insurance_num[$k2] - $renewal_insurance_num[$k2];
                if (empty($report_lost_num[$k2])) $report_lost_num[$k2] = 0;
                $data[$k2]['report_loss_num'] = $report_lost_num[$k2];
                if ($k2 == 0){
                    $data[$k2]['report_loss_num'] = $report_lost_total;
                }


            }
            sort($data);
            //先删除
            LeaseInsurance::where("rent_date","<",$time['end_time'])->where("rent_date",">=",$time['start_time'])->delete();
            //投保插入数据
            LeaseInsurance::insert($data);

            systemLog("同步租点投保统计任务成功!");
            //电池

        } catch (\Exception $e) {
            \Log::error("同步租点投保统计失败: {$e->getMessage()}");
            systemLog("同步租点投保统计失败: 详情请见laravel-log{$e->getMessage()}");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步租点投保统计失败：{$exception->getMessage()}");
        systemLog("同步租点投保统计失败: 详情请见laravel-log");
    }
}
