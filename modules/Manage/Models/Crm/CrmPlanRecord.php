<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/9/09 15:19
 */

namespace Modules\Manage\Models\Crm;

use App\Models\Manager;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Manage\Models\Model;

class CrmPlanRecord extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'type',
        'cus_id',
        'follow_mode',
        'content',
        'contact_id',
        'follow_user_ids',
        'follow_at',
        'created_by'
    ];

    //跟进记录
    const TYPE_ONE = 1;
    //跟进计划
    const TYPE_TWO = 2;

    public function createdUser()
    {
        return $this->hasOne(Manager::class, 'id', 'created_by')
            ->select('id', 'name');
    }

    public function cus()
    {
        return $this->hasOne(CrmUser::class, 'id', 'cus_id')
            ->select('id', 'name', 'mobile', 'charger_id');
    }

    public function images()
    {
        return $this->hasMany(CrmImage::class, 'resource_id', 'id');
    }

    public function contact()
    {
        return $this->hasOne(CrmContact::class, 'id', 'contact_id');
    }
}