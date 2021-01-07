<?php

namespace Modules\Lease\Models;

use Modules\Lease\Models\BaseModel as Model;

class BlFlow extends Model
{
    const APP_TYPE_ONE = 1;
    const APP_TYPE_ONE_TEXT = "用户端IOS";
    const APP_TYPE_TWO = 2;
    const APP_TYPE_TWO_TEXT = "用户端安卓";
    const APP_TYPE_THREE = 3;
    const APP_TYPE_THREE_TEXT = "网点端IOS";
    const APP_TYPE_FOUR = 4;
    const APP_TYPE_FOUR_TEXT = "网点端安卓";
    const APP_TYPE_FIVE = 5;
    const APP_TYPE_FIVE_TEXT = "仓库端安卓";
    const APP_TYPE_SIX = 6;
    const APP_TYPE_SIX_TEXT = "物流端安卓";
    const APP_TYPE_SEVEN = 7;
    const APP_TYPE_SEVEN_TEXT = "工厂端安卓";

    //租点涉及所有埋点
    public function allFlows()
    {
        return self::select("id", "day", "app_type", "system_type", "page_url", "user_id",
            "created_at")->orderBy("id")->get()->toArray();
    }

    //查询用户所在省份id
    public function user()
    {
        return $this->hasOne(BlUser::class, "id", "user_id")
            ->select("province_id", "id");
    }

    //查询用户所在省份id
    public function serviceUser()
    {
        return $this->hasOne(BlService::class, "id", "user_id")
            ->select("agent_id", "id");
    }
}
