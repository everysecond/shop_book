<?php

namespace Modules\Lease\Models;
use Modules\Lease\Models\BaseModel as Model;

class BlLeaseContract extends Model
{
    //客户信息
    public function user()
    {
        return $this->hasOne(BlUser::class,"id","user_id")
            ->select("id","mobile","nickname","created_at");
    }

    //网点信息
    public function service()
    {
        return $this->hasOne(BlService::class,"id","service_id")
            ->select("id","mobile","service_name","owner_name","agent_id","province_id", "province_name",
                "city_id","city_name","county_id","county_name","town_id","town_name","address","business_id");
    }

    //支付信息
    public function payment()
    {
        return $this->hasOne(BlLeasePayment::class,"contract_id","id")
            ->select("contract_id","type","status","payed_at","amount","rental");
    }
}
