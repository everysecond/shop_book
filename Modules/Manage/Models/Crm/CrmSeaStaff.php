<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/21 16:25
 */

namespace Modules\Manage\Models\Crm;

use App\Models\Manager;
use Modules\Manage\Models\Model;

class CrmSeaStaff extends Model
{
    protected $fillable = [
        "sea_id",
        "staff_id",
        "can_assign",
        "can_get"
    ];

    public function staff()
    {
        return $this->hasOne(Manager::class,"id","staff_id")->withTrashed();
    }
}