<?php

namespace Modules\Manage\Models\Report;

use Modules\Manage\Models\Model;

class LeaseUser extends Model
{
    protected $fillable = [
        "user_id",
        "user_id",
        "mobile",
        "mobile_model",
        "nickname",
        "age",
        "birthday",
        "sex",
        "register_type",
        "register_from",
        "province_id",
        "city_id",
        "county_id",
        "register_at",
        "register_hour",
        "created_at",
        "updated_at"
    ];
}
