<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/21 16:25
 */

namespace Modules\Manage\Models\Crm;

use App\Models\Manager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Manage\Models\Model;
use Modules\Manage\Models\Report\LeaseContract;
use Modules\Manage\Models\Report\LeasePayment;

class CrmUser extends Model
{
    use SoftDeletes;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable = [
        'name',
        'short_name',
        'cus_type',
        'cus_level',
        'cus_source',
        'mobile',
        'charger_id',
        'charger_name',
        'province_id',
        'province_name',
        'city_id',
        'city_name',
        'county_id',
        'county_name',
        'area',
        'address',
        'memo',
        'created_by',
        'first_contact',
        'first_contact_id',
        'first_contact_mobile'
    ];

    const FIELD_MEANS = [
        'name'          => '客户名称',
        'cus_type'      => '客户类型',
        'cus_level'     => '客户等级',
        'mobile'        => '客户电话',
        'charger_id'    => '负责人',
        'short_name'    => '助记名称',
        'province_id'   => '省',
        'province_name' => '省',
        'city_id'       => '市',
        'city_name'     => '市',
        'county_id'     => '区/县',
        'county_name'   => '区/县',
        'address'       => '详细地址',
        'memo'          => '备注'
    ];

    //类型 租点车主
    const CUS_TYPE_ONE = 1;
    //类型 租点网点
    const CUS_TYPE_TWO = 2;
    //类型 快点车主
    const CUS_TYPE_THREE = 3;
    //类型 快点网点
    const CUS_TYPE_FOUR = 4;

    const CUS_TYPE_ARR = [
        self::CUS_TYPE_ONE   => '租点用户',
        self::CUS_TYPE_TWO   => '租点网点',
        self::CUS_TYPE_THREE => '快点网点',
        self::CUS_TYPE_FOUR  => '快点网点'
    ];

    //客户等级 重点客户
    const CUS_LEVEL_ONE = 1;
    //客户等级 普通客户
    const CUS_LEVEL_TWO = 2;
    //客户等级 非优先客户
    const CUS_LEVEL_THREE = 3;

    //客户来源 crm APP录入
    const CUS_SOURCE_ONE = 1;
    //客户来源 租点系统
    const CUS_SOURCE_TWO = 2;
    //客户来源 中台录入
    const CUS_SOURCE_THREE = 3;

    const CUS_SOURCE_ARR = [
        self::CUS_SOURCE_ONE   => 'APP录入',
        self::CUS_SOURCE_TWO   => '租点系统',
        self::CUS_SOURCE_THREE => '中台录入'
    ];

    //历史未成交
    const CUS_DEAL_ONE = 1;
    //历史已成交
    const CUS_DEAL_TWO = 2;

    public function scopeType(Builder $query, $type)
    {
        return $query->where("cus_type", $type);
    }

    public function createUser()
    {
        return $this->hasOne(Manager::class, 'id', 'created_by');
    }

    public function contact()
    {
        return $this->hasOne(CrmContact::class, 'cus_id', 'id')->orderBy('id');
    }

    public function preFollow()
    {
        $now = time();
        return $this->hasOne(CrmPlanRecord::class, 'cus_id', 'id')
            ->where('follow_at', '<', $now)->orderBy('follow_at', 'desc');
    }

    public function nextFollow()
    {
        $now = time();
        return $this->hasOne(CrmPlanRecord::class, 'cus_id', 'id')
            ->where('follow_at', '>', $now)->orderBy('follow_at');
    }

    public function cPay()
    {
        return $this->hasOne(LeasePayment::class, 'user_id', 'user_id')
            ->where('status', LeasePayment::STATUS_INVALID);
    }


    public function contract()
    {
        return $this->hasOne(LeaseContract::class, 'user_id', 'user_id')
            ->orderBy('lease_expired_at', 'DESC');
    }

    public function track()
    {
        return $this->hasOne(CrmPlanRecord::class, 'cus_id', 'id');

    }
}