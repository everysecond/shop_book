<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Modules\Lease\Models\BlLeaseContract;
use Modules\Manage\Models\Report\LeaseContract;

class GenerateBigViewCache implements ShouldQueue
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
            $time = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));

            $num = BlLeaseContract::query()->from('bl_lease_contracts as a')
                ->selectRaw('count(a.id) as num,sum(b.rental) as rentalAll,sum(a.deposit) as depositAll,sum(a.single_num) as single_num')
                ->leftJoin('bl_lease_payments as b', 'a.id', '=', 'b.contract_id')
                ->whereIn('b.type', [LeaseContract::PAYMENT_TYPE_ONE, LeaseContract::PAYMENT_TYPE_THREE])
                ->where('b.status', LeaseContract::PAYMENT_STATUS_ONE);
            $clone = clone $num;
            $num = $num->where('a.created_at', '>=', $time)->first();
            $yesterdayData = $clone->whereBetween('a.created_at', [$yesterday, $time])->first();
            $data = [
                'num'=>[
                    'label' => '今日租赁量(组)',
                    'data'  => $num->num / $yesterdayData->num * 100,
                    'value' => $num->num
                ],
                'rental'=>[
                    'label' => '今日租赁金额(元)',
                    'data'  => $num->rentalAll / $yesterdayData->rentalAll * 100,
                    'value' => $num->rentalAll
                ],
                'deposit'=>[
                    'label' => '今日租赁押金(元)',
                    'data'  => $num->depositAll / $yesterdayData->depositAll * 100,
                    'value' => $num->depositAll
                ],
                'single_num'=>[
                    'label' => '今日回收电池(只)',
                    'data'  => $num->single_num / $yesterdayData->single_num * 100,
                    'value' => $num->single_num
                ]
            ];
            Cache::put('lease_actual_data',$data,3600);
            systemLog("生成数据大屏缓存任务成功");
        } catch (\Exception $e) {
            \Log::error("生成数据大屏缓存任务失败: {$e->getMessage()}");
            systemLog("生成数据大屏缓存任务失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成数据大屏缓存任务失败：{$exception->getMessage()}");
        systemLog("生成数据大屏缓存任务失败: 详情请见laravel-log");
    }
}
