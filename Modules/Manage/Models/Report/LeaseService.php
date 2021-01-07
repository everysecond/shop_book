<?php

namespace Modules\Manage\Models\Report;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Modules\Manage\Models\Crm\CrmUser;
use Modules\Manage\Models\Model;

class LeaseService extends Model
{
    use SpatialTrait;

    protected $spatialFields = ["location"];

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $dates = [
        "created_at"
    ];


    public function b_user()
    {
        return $this->hasOne(CrmUser::class,"user_id","id")->whereCusType(CrmUser::CUS_TYPE_TWO);
    }
}
