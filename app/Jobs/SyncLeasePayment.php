<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Lease\Models\BlLeasePayment;
use Modules\Manage\Models\Report\LeasePayment;

class SyncLeasePayment implements ShouldQueue
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
            $maxCreatedTime = LeasePayment::max("created_at");
            $maxUpdatedTime = LeasePayment::max("updated_at");
            BlLeasePayment::where(function ($query) use ($maxCreatedTime, $maxUpdatedTime) {
                //查询最近一小时新增或更新的合约订单
                if ($maxCreatedTime && $maxUpdatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime)
                        ->orWhere("updated_at", ">", $maxUpdatedTime);
                } elseif ($maxCreatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime);
                } elseif ($maxUpdatedTime) {
                    $query->where("updated_at", ">", $maxUpdatedTime);
                }
            })->chunk(100, function ($payments) {
                foreach ($payments as $payment) {
                    $syncPayment = app(LeasePayment::class)->find($payment["id"]);
                    if (!$syncPayment) {
                        $syncPayment = new LeasePayment();
                    }

                    $syncPayment->id = $payment->id;
                    $syncPayment->order_no = $payment->order_no;
                    $syncPayment->contract_id = $payment->contract_id;
                    $syncPayment->user_id = $payment->user_id;
                    $syncPayment->type = $payment->type;
                    $syncPayment->term_index = $payment->term_index;
                    $syncPayment->lease_term = $payment->lease_term;
                    $syncPayment->deposit = $payment->deposit;
                    $syncPayment->rental = $payment->rental;
                    $syncPayment->pre_balance = $payment->pre_balance;
                    $syncPayment->install_fee = $payment->install_fee;
                    $syncPayment->total_amount = $payment->total_amount;
                    $syncPayment->coupon_amount = $payment->coupon_amount;
                    $syncPayment->prepayment_amount = $payment->prepayment_amount;
                    $syncPayment->balance_amount = $payment->balance_amount;
                    $syncPayment->recycle_amount = $payment->recycle_amount;
                    $syncPayment->recycle_balance = $payment->recycle_balance;
                    $syncPayment->amount = $payment->amount;
                    $syncPayment->status = $payment->status;
                    $syncPayment->created_at = $payment->created_at;
                    $syncPayment->updated_at = $payment->updated_at;
                    $syncPayment->payed_at = $payment->payed_at;
                    $syncPayment->trade_no = $payment->trade_no;
                    $syncPayment->payment_id = $payment->payment_id;
                    $syncPayment->lease_service_id = $payment->lease_service_id;
                    $syncPayment->service_id = $payment->service_id;
                    $syncPayment->agent_id = $payment->agent_id;
                    $syncPayment->province_id = $payment->province_id;
                    $syncPayment->save();
                }
            });
            systemLog("同步租点新租订单成功");
        } catch (\Exception $e) {
            \Log::error("同步租点新租订单失败: {$e->getMessage()}");
            systemLog("同步租点新租订单失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步租点新租订单失败：{$exception->getMessage()}");
        systemLog("同步租点新租订单失败: 详情请见laravel-log");
    }
}
