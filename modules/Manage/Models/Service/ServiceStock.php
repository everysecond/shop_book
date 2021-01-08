<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/12/26 14:39
 */

namespace Modules\Manage\Models\Service;

use Modules\Manage\Models\Model;

class ServiceStock extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    const LEASE_TYPE_ONE = 1;
    const LEASE_TYPE_ONE_TEXT = '全新电池';

    const LEASE_TYPE_TWO = 2;
    const LEASE_TYPE_TWO_TEXT = '备用电池';

    const LEASE_TYPE_FOUR = 4;
    const LEASE_TYPE_FOUR_TEXT = '废旧电池';

    const LEASE_TYPE_THREE = 0;
    const LEASE_TYPE_THREE_TEXT = '退回电池';


}