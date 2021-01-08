<?php

namespace Modules\Manage\Models\Crm;

use App\Models\Manager;
use Modules\Manage\Models\Model;

class CrmTeamList extends Model
{
    //负责人角色
    const ROLE_ONE = 1;
    //协作成员
    const ROLE_TWO = 2;

    public function manager()
    {
        return $this->hasOne(Manager::class, 'id', 'user_id');
    }

    //获取客户所有归属团队成员
    public static function getCusTeamers($cusId)
    {
        $data = self::query()->with('manager')->where('customer_id', $cusId)->get();
        $teams = [];
        foreach ($data as $datum) {
            $teams[$datum->user_id] = $datum->manager->name;
        }
        return $teams;
    }
}