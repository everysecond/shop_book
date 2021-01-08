<?php

namespace Modules\Manage\Models\Report;
use Modules\Manage\Models\Model;

class LeasePayment extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    const STATUS_INVALID = 1;
    const STATUS_INVALID_TEXT = '未生效';

    const STATUS_WAIT = 2;
    const STATUS_WAIT_TEXT = '待生效';

    const STATUS_VALID = 3;
    const STATUS_VALID_TEXT = '已生效';

    const STATUS_RETIRED = 4;
    const STATUS_RETIRED_TEXT = '已退租';

    const STATUS_LOST = 5;
    const STATUS_LOST_TEXT = '已丢失';

    public function contract()
    {
        return $this->hasOne(LeaseContract::class,"id","contract_id")
            ->select("id","lease_term","lease_unit");
    }
}
