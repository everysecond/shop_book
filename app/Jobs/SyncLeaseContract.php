<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;
use Modules\Lease\Models\BlLeaseContract;
use Modules\Manage\Models\Report\LeaseContract;

class SyncLeaseContract implements ShouldQueue
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
            $maxCreatedTime = LeaseContract::max("created_at");
            $maxUpdatedTime = LeaseContract::max("updated_at");
            $agents = leaseAgentCache();
            BlLeaseContract::where(function ($query) use ($maxCreatedTime, $maxUpdatedTime) {
                //查询最近一小时新增或更新的合约订单
                if ($maxCreatedTime && $maxUpdatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime)
                        ->orWhere("updated_at", ">", $maxUpdatedTime);
                } elseif ($maxCreatedTime) {
                    $query->where("created_at", ">", $maxCreatedTime);
                } elseif ($maxUpdatedTime) {
                    $query->where("updated_at", ">", $maxUpdatedTime);
                }
            })->with(["user", "service", "payment" => function ($query) {
                $query->where("type", LeaseContract::PAYMENT_TYPE_ONE)->where("status", LeaseContract::PAYMENT_STATUS_ONE);
            }])->chunk(100, function ($contracts) use ($agents) {
                $contracts = $contracts->toArray();
                foreach ($contracts as $k => $contract) {
                    $syncContract = LeaseContract::where("contract_no", $contract["contract_no"])->first();
                    if (!$syncContract) {
                        $syncContract = new LeaseContract();
                    }
                    //冗余用户相关信息
                    if ($contract["user"]) {
                        $syncContract->user_mobile = $contract["user"]["mobile"] ? $contract["user"]["mobile"] : "";
                        $syncContract->user_nickname = $contract["user"]["nickname"];
                        $syncContract->user_register_at = date("Y-m-d", strtotime($contract["user"]["created_at"]));
                    } else {
                        $syncContract->user_mobile = "";
                        $syncContract->user_nickname = "";
                        $syncContract->user_register_at = "";
                    }
                    $syncContract->created_date = date("Y-m-d", strtotime($contract["created_at"]));
                    unset($contract["user"]);

                    $syncContract->rental_all = 0;
                    if ($json = $contract["rentals"]) {
                        $rentalArr = json_decode($json, true);
                        if ($contract['lease_expired_at'] == $contract['contract_expired_at']) {
                            $syncContract->rental_all = array_sum($rentalArr);
                        } else {
                            $syncContract->rental_all = $rentalArr[0];
                        }
                    }

                    //冗余合约支付信息
                    if ($contract["payment"]) {
                        $syncContract->payment_type = $contract["payment"]["type"];
                        $syncContract->payment_status = $contract["payment"]["status"];
                        $syncContract->payment_payed_at = $contract["payment"]["payed_at"];
                        $syncContract->payment_amount = $contract["payment"]["amount"];
                    } else {
                        $syncContract->payment_type = 0;
                        $syncContract->payment_status = 0;
                        $syncContract->payment_payed_at = null;
                        $syncContract->payment_amount = 0.00;
                    }
                    unset($contract["payment"]);
                    //合约周期统一换算为月
                    if ($contract["contract_unit"] == "year") {
                        $syncContract->contract_term = $contract["contract_term"] * 12;
                    } else {
                        $syncContract->contract_term = $contract["contract_term"];
                    }
                    unset($contract["contract_term"]);
                    $syncContract->contract_unit = "month";
                    unset($contract["contract_unit"]);
                    //冗余服务点（网点）信息
                    if ($contract["service"]) {
                        $syncContract->service_name = $contract["service"]["service_name"];
                        $syncContract->service_mobile = $contract["service"]["mobile"];
                        $syncContract->service_owner_name = $contract["service"]["owner_name"];
                        $syncContract->service_agent_id = $contract["service"]["agent_id"];
                        $syncContract->service_province_id = $contract["service"]["province_id"];
                        $syncContract->service_province_name = $contract["service"]["province_name"];
                        $syncContract->service_city_id = $contract["service"]["city_id"];
                        $syncContract->service_city_name = $contract["service"]["city_name"];
                        $syncContract->service_county_id = $contract["service"]["county_id"];
                        $syncContract->service_county_name = $contract["service"]["county_name"];
                        $syncContract->service_town_id = $contract["service"]["town_id"];
                        $syncContract->service_town_name = $contract["service"]["town_name"];
                        $syncContract->service_address = $contract["service"]["address"];
                        $syncContract->service_business_id = $contract["service"]["business_id"];
                    } else {
                        $syncContract->service_name = "";
                        $syncContract->service_mobile = "";
                        $syncContract->service_owner_name = "";
                        $syncContract->service_agent_id = 0;
                        $syncContract->service_province_id = 0;
                        $syncContract->service_province_name = "";
                        $syncContract->service_city_id = 0;
                        $syncContract->service_city_name = "";
                        $syncContract->service_county_id = 0;
                        $syncContract->service_county_name = "";
                        $syncContract->service_town_id = 0;
                        $syncContract->service_town_name = "";
                        $syncContract->service_address = "";
                        $syncContract->service_business_id = 0;
                    }
                    unset($contract["service"]);
                    $syncContract->id = $contract['id'];
                    $syncContract->user_id = $contract['user_id'];
                    $syncContract->contract_no = $contract['contract_no'];
                    $syncContract->model_id = $contract['model_id'];
                    $syncContract->model_name = $contract['model_name'];
                    $syncContract->single_model = $contract['single_model'];
                    $syncContract->single_num = $contract['single_num'];
                    $syncContract->deposit = $contract['deposit'];
                    $syncContract->status = $contract['status'];
                    $syncContract->lease_service_id = $contract['lease_service_id'];
                    $syncContract->lease_term = $contract['lease_term'];
                    $syncContract->lease_unit = $contract['lease_unit'];
                    $syncContract->term_index = $contract['term_index'];
                    $syncContract->created_at = $contract['created_at'];
                    $syncContract->updated_at = $contract['updated_at'];
                    $syncContract->effected_at = $contract['effected_at'];
                    $syncContract->contract_expired_at = $contract['contract_expired_at'];
                    $syncContract->lease_expired_at = $contract['lease_expired_at'];
                    $syncContract->retired_at = $contract['retired_at'];
                    $syncContract->prev_id = $contract['prev_id'];
                    $syncContract->root_id = $contract['root_id'];
                    $syncContract->rentals = $contract['rentals'];
                    $syncContract->group_code = $contract['group_code'];
                    $syncContract->service_id = $contract['service_id'];
                    $syncContract->recycle_model = $contract['recycle_model'];
                    $syncContract->recycle_model_id = $contract['recycle_model_id'];
                    $syncContract->recycle_price = $contract['recycle_price'];
                    $syncContract->remark = $contract['remark'];
                    $syncContract->service_reward = $contract['service_reward'];
                    $syncContract->prepayment = $contract['prepayment'];
                    $syncContract->pre_balance = $contract['pre_balance'];
                    $syncContract->agent_id = $contract['agent_id'];
                    if($agent = Arr::get($agents,$contract['agent_id'])){
                        $syncContract->county_id = $agent['county_id'];
                        $syncContract->city_id = $agent['city_id'];
                    }
                    $syncContract->province_id = $contract['province_id'];
                    $syncContract->user_mobile = $contract['user_mobile'] ?? $syncContract->user_mobile;
                    $syncContract->order_scan_time = $contract['order_scan_time'];
                    $syncContract->battery_scan_time = $contract['battery_scan_time'];
                    $syncContract->install_payed_time = $contract['install_payed_time'];
                    $syncContract->install_sure_time = $contract['install_sure_time'];
                    $syncContract->install_payed_date = $contract['install_payed_time'] ? date('Y-m-d', $contract['install_payed_time']) : '';
                    $syncContract->deposit_price = $contract['deposit_price'];
                    $syncContract->lease_type = $contract['lease_type'];
                    $syncContract->save();
                }
            });
            systemLog("同步租点合约订单成功");
        } catch (\Exception $e) {
            \Log::error("同步租点合约订单失败: {$e->getMessage()}");
            systemLog("同步租点合约订单失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("同步租点合约订单失败：{$exception->getMessage()}");
        systemLog("同步租点合约订单失败: 详情请见laravel-log");
    }
}
