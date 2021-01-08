<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/21 16:25
 */

namespace Modules\Manage\Models\Crm;

use App\Models\Manager;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Manage\Models\Model;

class CrmContact extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'cus_id',
        'mobile',
        'name',
        'wechat',
        'position',
        'is_key',
        'sex',
        'agent_id',
        'province_id',
        'address',
        'memo',
        'created_by'
    ];

    public function createdUser()
    {
        return $this->hasOne(Manager::class, 'id', 'created_by')
            ->select('id', 'name');
    }

    public function cus()
    {
        return $this->hasOne(CrmUser::class, 'id', 'cus_id')
            ->select('id', 'name', 'mobile','charger_id');
    }
}