<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/8 11:08
 */

namespace Modules\Manage\Models\Service;
use Modules\Manage\Models\Model;

class LeaseServiceBalanceLog extends Model
{
    public $timestamps = false;
    public $dateFormat = "Y-m-d H:i:s";
    //资金来源 佣金类型
    const SOURCE_FOUR =4;
}