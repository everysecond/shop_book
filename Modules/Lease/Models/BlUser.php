<?php

namespace Modules\Lease\Models;

use Modules\Lease\Models\BaseModel as Model;
use Modules\Manage\Models\Report\LeaseContract;

class BlUser extends Model
{
    public function hasPay()
    {
        return $this->hasOne(BlLeasePayment::class, 'user_id', 'id')
            ->where('status', 1);
    }

    public function lastContract()
    {
        return $this->hasOne(BlLeaseContract::class, 'user_id', 'id')
            ->whereIn('status', [
                LeaseContract::STATUS_VALID,
                LeaseContract::STATUS_RENEWED,
                LeaseContract::STATUS_EXCHANGED
            ])->orderBy('lease_expired_at', 'desc');
    }

    public function hasValidContract()
    {
//        return $this->hasOne(BlLeaseContract::class, 'user_id', 'id')
//            ->whereIn('status', [
//                LeaseContract::STATUS_VALID,
//                LeaseContract::STATUS_WAIT
//            ])->where('deposit', '>', 0);
        $time = now();
        return $this->hasOne(BlLeaseContract::class, 'user_id', 'id')
            ->whereIn('status', [
                LeaseContract::STATUS_VALID,
                LeaseContract::STATUS_WAIT,
                LeaseContract::STATUS_RENEWED,
                LeaseContract::STATUS_EXCHANGED
            ])->where('lease_expired_at', '>', $time);
    }
}
